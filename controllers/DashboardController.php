<?php

class DashboardController extends Controller
{
    private Donation $donations;
    private User $users;

    public function __construct()
    {
        $this->donations = new Donation();
        $this->users = new User();
    }

    public function home(): void
    {
        $this->view('home');
    }

    public function index(): void
    {
        $user = $this->requireAuth();

        if ($user['role'] === 'admin') {
            $donations = $this->donations->allWithRelations();
            $orphanages = $this->users->allByRole('orphanage');
            $suggestions = [];
            foreach ($donations as $d) {
                $suggestions[$d['id']] = $this->donations->nearestOrphanage($d['pickup_latitude'] !== null ? (float) $d['pickup_latitude'] : null, $d['pickup_longitude'] !== null ? (float) $d['pickup_longitude'] : null);
            }
            $this->view('dashboard/admin', ['donations' => $donations, 'orphanages' => $orphanages, 'suggestions' => $suggestions, 'csrf' => Csrf::token()]);
            return;
        }

        if ($user['role'] === 'orphanage') {
            $this->view('dashboard/orphanage', ['donations' => $this->donations->byOrphanage((int) $user['id']), 'csrf' => Csrf::token()]);
            return;
        }

        $this->view('dashboard/user', ['donations' => $this->donations->byDonor((int) $user['id']), 'csrf' => Csrf::token()]);
    }
}
