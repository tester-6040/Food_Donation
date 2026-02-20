<?php

class DonationController extends Controller
{
    private Donation $donations;
    private User $users;
    private Mailer $mailer;

    public function __construct()
    {
        $this->donations = new Donation();
        $this->users = new User();
        $this->mailer = new Mailer();
    }

    public function create(): void
    {
        $user = $this->requireAuth('user');
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/dashboard');
        }

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $quantity = trim($_POST['quantity'] ?? '');
        $pickupAddress = trim($_POST['pickup_address'] ?? '');
        if ($title === '' || $description === '' || $quantity === '' || $pickupAddress === '') {
            Session::flash('error', 'All donation fields are required.');
            $this->redirect('/dashboard');
        }

        $donationId = $this->donations->create([
            'donor_id' => $user['id'],
            'title' => $title,
            'description' => $description,
            'quantity' => $quantity,
            'pickup_address' => $pickupAddress,
            'pickup_latitude' => trim($_POST['pickup_latitude'] ?? ''),
            'pickup_longitude' => trim($_POST['pickup_longitude'] ?? ''),
        ]);

        $app = require __DIR__ . '/../config/app.php';
        $admins = array_values(array_unique($app['mail']['admin_recipients']));
        $this->mailer->send($admins, 'New Donation Submitted', "Donation #{$donationId} has been submitted and is pending admin assignment.");
        $this->mailer->send($user['email'], 'Donation Received', "Thank you {$user['name']}, your donation #{$donationId} is pending review.");

        Session::flash('success', 'Donation submitted successfully.');
        $this->redirect('/dashboard');
    }

    public function assign(): void
    {
        $this->requireAuth('admin');
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/dashboard');
        }

        $donationId = (int) ($_POST['donation_id'] ?? 0);
        $donation = $this->donations->find($donationId);
        if (!$donation || $donation['status'] !== 'pending') {
            Session::flash('error', 'Donation cannot be assigned.');
            $this->redirect('/dashboard');
        }

        $orphanageId = (int) ($_POST['orphanage_id'] ?? 0);
        if ($orphanageId <= 0) {
            $nearest = $this->donations->nearestOrphanage($donation['pickup_latitude'] !== null ? (float) $donation['pickup_latitude'] : null, $donation['pickup_longitude'] !== null ? (float) $donation['pickup_longitude'] : null);
            $orphanageId = (int) ($nearest['id'] ?? 0);
        }

        if ($orphanageId <= 0 || !$this->donations->updateAssignment($donationId, $orphanageId)) {
            Session::flash('error', 'Unable to assign orphanage.');
            $this->redirect('/dashboard');
        }

        $orphanage = $this->users->findById($orphanageId);
        if ($orphanage) {
            $this->mailer->send($orphanage['email'], 'Donation Assigned', "Donation #{$donationId} has been assigned to your orphanage. Please accept or reject.");
        }

        Session::flash('success', 'Donation assigned successfully.');
        $this->redirect('/dashboard');
    }

    public function orphanageAction(): void
    {
        $orphanage = $this->requireAuth('orphanage');
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/dashboard');
        }

        $donationId = (int) ($_POST['donation_id'] ?? 0);
        $action = $_POST['action'] ?? '';
        $newStatus = $action === 'accept' ? 'accepted' : ($action === 'reject' ? 'rejected' : '');

        $donation = $this->donations->find($donationId);
        if (!$donation || (int) $donation['assigned_orphanage_id'] !== (int) $orphanage['id']) {
            Session::flash('error', 'Donation not found for your orphanage.');
            $this->redirect('/dashboard');
        }

        if ($newStatus === '' || $donation['status'] !== 'pending' || !$this->donations->transitionStatus($donationId, $newStatus)) {
            Session::flash('error', 'Invalid action or donation already finalized.');
            $this->redirect('/dashboard');
        }

        $donor = $this->users->findById((int) $donation['donor_id']);
        if ($donor) {
            $this->mailer->send($donor['email'], 'Donation Status Updated', "Your donation #{$donationId} has been {$newStatus} by {$orphanage['name']}.");
        }

        Session::flash('success', 'Donation status updated.');
        $this->redirect('/dashboard');
    }
}
