<?php

class DonationController extends Controller
{
    private Donation $donations;
    private User $users;

    public function __construct()
    {
        $this->donations = new Donation();
        $this->users = new User();
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

        $adminBody = $this->htmlMail('New Donation Submitted', [
            "Donation ID: #{$donationId}",
            "Donor: {$user['name']} ({$user['email']})",
            "Title: {$title}",
            "Quantity: {$quantity}",
            "Pickup Address: {$pickupAddress}",
            'Coordinates: ' . ($pickupLat !== '' && $pickupLng !== '' ? "{$pickupLat}, {$pickupLng}" : 'Not provided'),
            'Status: pending',
            'Please assign this donation to an orphanage from admin dashboard.',
        ]);

        $donorBody = $this->htmlMail('Thanks for Donating', [
            "Dear {$user['name']},",
            'Your donation has been received successfully.',
            "Donation ID: #{$donationId}",
            "Food: {$title}",
            "Quantity: {$quantity}",
            "Pickup Address: {$pickupAddress}",
            'Current Status: pending review by admin',
            'We appreciate your support in feeding children in need.',
        ]);

        $this->sendMail($admins, 'New Donation Submitted', $adminBody, $app['mail']);
        $this->sendMail($user['email'], 'Thanks for Donating', $donorBody, $app['mail']);

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
            $app = require __DIR__ . '/../config/app.php';
            $orphanageBody = $this->htmlMail('Donation Assigned for You', [
                "Dear {$orphanage['name']},",
                'A new donation has been assigned to your orphanage.',
                "Donation ID: #{$donationId}",
                "Food: {$donation['title']}",
                "Quantity: {$donation['quantity']}",
                "Pickup Address: {$donation['pickup_address']}",
                'Please login and Accept/Reject this donation.',
            ]);
            $this->sendMail($orphanage['email'], 'Donation Assigned for You', $orphanageBody, $app['mail']);
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
            $app = require __DIR__ . '/../config/app.php';
            $donorBody = $this->htmlMail('Donation Status Updated', [
                "Dear {$donor['name']},",
                "Your donation #{$donationId} has been {$newStatus} by {$orphanage['name']}.",
                "Food: {$donation['title']}",
                "Pickup Address: {$donation['pickup_address']}",
                'Thank you again for your valuable support.',
            ]);
            $this->sendMail($donor['email'], 'Donation Status Updated', $donorBody, $app['mail']);
        }

        Session::flash('success', 'Donation status updated.');
        $this->redirect('/dashboard');
    }

    private function adminRecipients(array $app): array
    {
        $mail = $app['mail'] ?? [];
        $recipients = $mail['admin_recipients'] ?? [];
        if (!empty($mail['admin_email'])) {
            $recipients[] = $mail['admin_email'];
        }
        if (!empty($mail['secondary_admin_email'])) {
            $recipients[] = $mail['secondary_admin_email'];
        }
        return array_values(array_unique(array_filter($recipients)));
    }

    private function sendMail(array|string $to, string $subject, string $message, array $mailConfig): void
    {
        foreach ((array) $to as $email) {
            if (!is_string($email) || $email === '') {
                continue;
            }
            if (class_exists('Core\\Mailer')) {
                \Core\Mailer::send($email, $subject, $message, $mailConfig);
            } else {
                $line = sprintf("[%s] Core\\Mailer missing TO:%s SUBJECT:%s\n", date('c'), $email, $subject);
                @file_put_contents(__DIR__ . '/../storage/mail.log', $line, FILE_APPEND);
            }
        }
    }

    private function htmlMail(string $heading, array $lines): string
    {
        $safeHeading = htmlspecialchars($heading, ENT_QUOTES, 'UTF-8');
        $items = '';
        foreach ($lines as $line) {
            $items .= '<li style="margin:6px 0;">' . htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . '</li>';
        }

        return '<div style="font-family:Arial,sans-serif;line-height:1.5;color:#111">'
            . '<h2 style="color:#0f766e;margin-bottom:10px;">' . $safeHeading . '</h2>'
            . '<ul style="padding-left:18px;margin:0;">' . $items . '</ul>'
            . '<p style="margin-top:14px;color:#475569;">Food Donation Platform</p>'
            . '</div>';
    }
}
