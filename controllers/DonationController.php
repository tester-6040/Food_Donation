<?php

class DonationController extends Controller
{
    private Donation $donations;
    private User $users;
    private mixed $mailer;

    public function __construct()
    {
        $this->donations = new Donation();
        $this->users = new User();
        $this->mailer = class_exists('Mailer') ? new Mailer() : null;
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

        $pickupLat = trim($_POST['pickup_latitude'] ?? '');
        $pickupLng = trim($_POST['pickup_longitude'] ?? '');

        $donationId = $this->donations->create([
            'donor_id' => $user['id'],
            'title' => $title,
            'description' => $description,
            'quantity' => $quantity,
            'pickup_address' => $pickupAddress,
            'pickup_latitude' => $pickupLat,
            'pickup_longitude' => $pickupLng,
        ]);

        $app = require __DIR__ . '/../config/app.php';
        $admins = $this->adminRecipients($app);

        $adminBody = "New Donation Submitted\n"
            . "----------------------\n"
            . "Donation ID: #{$donationId}\n"
            . "Donor: {$user['name']} ({$user['email']})\n"
            . "Title: {$title}\n"
            . "Quantity: {$quantity}\n"
            . "Pickup Address: {$pickupAddress}\n"
            . "Coordinates: " . ($pickupLat !== '' && $pickupLng !== '' ? "{$pickupLat}, {$pickupLng}" : 'Not provided') . "\n"
            . "Status: pending\n"
            . "\nPlease assign this donation to an orphanage from admin dashboard.";

        $donorBody = "Thank You for Donating\n"
            . "----------------------\n"
            . "Dear {$user['name']},\n"
            . "Your donation has been received successfully.\n"
            . "Donation ID: #{$donationId}\n"
            . "Food: {$title}\n"
            . "Quantity: {$quantity}\n"
            . "Pickup Address: {$pickupAddress}\n"
            . "Current Status: pending review by admin\n"
            . "\nWe appreciate your support in feeding children in need.";

        $this->sendMail($admins, 'New Donation Submitted', $adminBody, $app);
        $this->sendMail($user['email'], 'Thanks for Donating', $donorBody, $app);

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
            $orphanageBody = "Donation Assigned for You\n"
                . "------------------------\n"
                . "Dear {$orphanage['name']},\n"
                . "A new donation has been assigned to your orphanage.\n"
                . "Donation ID: #{$donationId}\n"
                . "Food: {$donation['title']}\n"
                . "Quantity: {$donation['quantity']}\n"
                . "Pickup Address: {$donation['pickup_address']}\n"
                . "\nPlease login and Accept/Reject this donation.";
            $app = require __DIR__ . '/../config/app.php';
            $this->sendMail($orphanage['email'], 'Donation Assigned for You', $orphanageBody, $app);
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
            $donorBody = "Donation Status Update\n"
                . "---------------------\n"
                . "Dear {$donor['name']},\n"
                . "Your donation #{$donationId} has been {$newStatus} by {$orphanage['name']}.\n"
                . "Food: {$donation['title']}\n"
                . "Pickup Address: {$donation['pickup_address']}\n"
                . "\nThank you again for your valuable support.";
            $app = require __DIR__ . '/../config/app.php';
            $this->sendMail($donor['email'], 'Donation Status Updated', $donorBody, $app);
        }

        Session::flash('success', 'Donation status updated.');
        $this->redirect('/dashboard');
    }

    private function adminRecipients(array $app): array
    {
        $mail = $app['mail'] ?? [];
        $recipients = $mail['admin_recipients'] ?? [];
        if (isset($mail['admin_email'])) {
            $recipients[] = $mail['admin_email'];
        }
        if (isset($mail['secondary_admin_email'])) {
            $recipients[] = $mail['secondary_admin_email'];
        }
        return array_values(array_unique(array_filter($recipients)));
    }

    private function sendMail(array|string $to, string $subject, string $message, array $app): void
    {
        if (is_object($this->mailer) && method_exists($this->mailer, 'send')) {
            $this->mailer->send($to, $subject, $message);
            return;
        }

        if (class_exists('Core\\Mailer') && method_exists('Core\\Mailer', 'send') && class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            foreach ((array) $to as $email) {
                if (is_string($email) && $email !== '') {
                    \Core\Mailer::send($email, $subject, $message, $app['mail'] ?? []);
                }
            }
            return;
        }

        if (class_exists('Mailer')) {
            $fallback = new Mailer();
            $fallback->send($to, $subject, $message);
            return;
        }

        $line = sprintf("[%s] MAILER_UNAVAILABLE TO:%s | SUBJECT:%s\n", date('c'), implode(',', (array) $to), $subject);
        @file_put_contents(__DIR__ . '/../storage/mail.log', $line, FILE_APPEND);
    }
}
