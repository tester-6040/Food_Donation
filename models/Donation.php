<?php

class Donation extends BaseModel
{
    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO donations (donor_id, title, description, quantity, pickup_address, pickup_latitude, pickup_longitude, status) VALUES (:donor_id, :title, :description, :quantity, :pickup_address, :pickup_latitude, :pickup_longitude, :status)');
        $stmt->execute([
            ':donor_id' => $data['donor_id'],
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':quantity' => $data['quantity'],
            ':pickup_address' => $data['pickup_address'],
            ':pickup_latitude' => $data['pickup_latitude'] !== '' ? $data['pickup_latitude'] : null,
            ':pickup_longitude' => $data['pickup_longitude'] !== '' ? $data['pickup_longitude'] : null,
            ':status' => 'pending',
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function allWithRelations(): array
    {
        $sql = 'SELECT d.*, donor.name AS donor_name, donor.email AS donor_email, o.name AS orphanage_name, o.email AS orphanage_email
                FROM donations d
                JOIN users donor ON donor.id = d.donor_id
                LEFT JOIN users o ON o.id = d.assigned_orphanage_id
                ORDER BY d.id DESC';
        return $this->db->query($sql)->fetchAll();
    }

    public function byDonor(int $donorId): array
    {
        $stmt = $this->db->prepare('SELECT d.*, o.name AS orphanage_name FROM donations d LEFT JOIN users o ON o.id = d.assigned_orphanage_id WHERE d.donor_id = :donor_id ORDER BY d.id DESC');
        $stmt->execute([':donor_id' => $donorId]);
        return $stmt->fetchAll();
    }

    public function byOrphanage(int $orphanageId): array
    {
        $stmt = $this->db->prepare('SELECT d.*, u.name AS donor_name, u.email AS donor_email FROM donations d JOIN users u ON u.id = d.donor_id WHERE d.assigned_orphanage_id = :id ORDER BY d.id DESC');
        $stmt->execute([':id' => $orphanageId]);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM donations WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function updateAssignment(int $id, int $orphanageId): bool
    {
        $stmt = $this->db->prepare('UPDATE donations SET assigned_orphanage_id = :oid, assigned_at = NOW() WHERE id = :id AND status = :status');
        return $stmt->execute([':oid' => $orphanageId, ':id' => $id, ':status' => 'pending']);
    }

    public function transitionStatus(int $id, string $newStatus): bool
    {
        $stmt = $this->db->prepare('UPDATE donations SET status = :new_status, decided_at = NOW() WHERE id = :id AND status = :old_status');
        return $stmt->execute([':new_status' => $newStatus, ':id' => $id, ':old_status' => 'pending']);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM donations WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function nearestOrphanage(?float $lat, ?float $lng): ?array
    {
        if ($lat === null || $lng === null) {
            return null;
        }

        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            return null;
        }

        $sql = 'SELECT id, name, email, address, latitude, longitude,
                (6371 * ACOS(COS(RADIANS(:lat1)) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS(:lng)) + SIN(RADIANS(:lat2)) * SIN(RADIANS(latitude)))) AS distance_km
                FROM users
                WHERE role = :role AND latitude IS NOT NULL AND longitude IS NOT NULL
                ORDER BY distance_km ASC
                LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':lat1' => $lat, ':lat2' => $lat, ':lng' => $lng, ':role' => 'orphanage']);
        return $stmt->fetch() ?: null;
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM donations ORDER BY id DESC')->fetchAll();
    }
}
