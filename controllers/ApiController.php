<?php

class ApiController
{
    private User $users;
    private Donation $donations;

    public function __construct()
    {
        $this->users = new User();
        $this->donations = new Donation();
    }

    public function users(string $method): void
    {
        if ($method === 'GET') {
            $this->respond(true, $this->users->all());
            return;
        }

        $body = $this->body();
        if ($method === 'POST') {
            if (empty($body['name']) || empty($body['email']) || empty($body['password'])) {
                $this->respond(false, null, 'Missing required fields', 422);
                return;
            }
            $this->users->create([
                'name' => trim($body['name']),
                'email' => trim($body['email']),
                'password' => (string) $body['password'],
                'role' => in_array($body['role'] ?? 'user', ['user', 'orphanage', 'admin'], true) ? $body['role'] : 'user',
                'address' => trim((string) ($body['address'] ?? '')),
                'latitude' => (string) ($body['latitude'] ?? ''),
                'longitude' => (string) ($body['longitude'] ?? ''),
            ]);
            $this->respond(true, ['message' => 'User created']);
            return;
        }

        if (in_array($method, ['PUT', 'PATCH'], true)) {
            $id = (int) ($body['id'] ?? 0);
            if ($id <= 0 || empty($body['role'])) {
                $this->respond(false, null, 'id and role are required', 422);
                return;
            }
            $ok = $this->users->updateRole($id, $body['role']);
            $this->respond($ok, ['message' => $ok ? 'User updated' : 'User update failed'], $ok ? null : 'Unable to update user', $ok ? 200 : 400);
            return;
        }

        if ($method === 'DELETE') {
            $id = (int) ($body['id'] ?? ($_GET['id'] ?? 0));
            $ok = $id > 0 ? $this->users->delete($id) : false;
            $this->respond($ok, ['message' => $ok ? 'User deleted' : 'User delete failed'], $ok ? null : 'Unable to delete user', $ok ? 200 : 400);
            return;
        }

        $this->respond(false, null, 'Method not allowed', 405);
    }

    public function donations(string $method): void
    {
        if ($method === 'GET') {
            $this->respond(true, $this->donations->allWithRelations());
            return;
        }

        $body = $this->body();
        if ($method === 'POST') {
            $required = ['donor_id', 'title', 'description', 'quantity', 'pickup_address'];
            foreach ($required as $field) {
                if (empty($body[$field])) {
                    $this->respond(false, null, "{$field} is required", 422);
                    return;
                }
            }
            $id = $this->donations->create([
                'donor_id' => (int) $body['donor_id'],
                'title' => trim($body['title']),
                'description' => trim($body['description']),
                'quantity' => trim($body['quantity']),
                'pickup_address' => trim($body['pickup_address']),
                'pickup_latitude' => (string) ($body['pickup_latitude'] ?? ''),
                'pickup_longitude' => (string) ($body['pickup_longitude'] ?? ''),
            ]);
            $this->respond(true, ['id' => $id]);
            return;
        }

        if (in_array($method, ['PUT', 'PATCH'], true)) {
            $id = (int) ($body['id'] ?? 0);
            $status = $body['status'] ?? '';
            if ($id <= 0 || !in_array($status, ['accepted', 'rejected'], true)) {
                $this->respond(false, null, 'id and valid final status required', 422);
                return;
            }
            $ok = $this->donations->transitionStatus($id, $status);
            $this->respond($ok, ['message' => $ok ? 'Donation status updated' : 'Invalid status transition'], $ok ? null : 'Transition rejected', $ok ? 200 : 400);
            return;
        }

        if ($method === 'DELETE') {
            $id = (int) ($body['id'] ?? ($_GET['id'] ?? 0));
            $ok = $id > 0 ? $this->donations->delete($id) : false;
            $this->respond($ok, ['message' => $ok ? 'Donation deleted' : 'Delete failed'], $ok ? null : 'Unable to delete donation', $ok ? 200 : 400);
            return;
        }

        $this->respond(false, null, 'Method not allowed', 405);
    }

    public function orphanageActions(string $method): void
    {
        if ($method !== 'POST') {
            $this->respond(false, null, 'Method not allowed', 405);
            return;
        }

        $body = $this->body();
        $id = (int) ($body['donation_id'] ?? 0);
        $status = ($body['action'] ?? '') === 'accept' ? 'accepted' : (($body['action'] ?? '') === 'reject' ? 'rejected' : '');
        if ($id <= 0 || $status === '') {
            $this->respond(false, null, 'donation_id and valid action are required', 422);
            return;
        }

        $ok = $this->donations->transitionStatus($id, $status);
        $this->respond($ok, ['message' => $ok ? 'Action applied' : 'Invalid transition'], $ok ? null : 'Transition rejected', $ok ? 200 : 400);
    }

    private function body(): array
    {
        $raw = file_get_contents('php://input') ?: '';
        if ($raw === '') {
            return $_POST;
        }

        $decoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        parse_str($raw, $parsed);
        return is_array($parsed) ? $parsed : [];
    }

    private function respond(bool $success, mixed $data = null, ?string $error = null, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode(['success' => $success, 'data' => $data ?? new stdClass(), 'error' => $error], JSON_UNESCAPED_UNICODE);
    }
}
