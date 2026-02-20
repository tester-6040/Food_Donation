<?php

class User extends BaseModel
{
    public function create(array $data): bool
    {
        $stmt = $this->db->prepare('INSERT INTO users (name, email, password, role, address, latitude, longitude) VALUES (:name, :email, :password, :role, :address, :latitude, :longitude)');
        return $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':role' => $data['role'],
            ':address' => $data['address'] ?? null,
            ':latitude' => $data['latitude'] !== '' ? $data['latitude'] : null,
            ':longitude' => $data['longitude'] !== '' ? $data['longitude'] : null,
        ]);
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function allByRole(string $role): array
    {
        $stmt = $this->db->prepare('SELECT id, name, email, address, latitude, longitude FROM users WHERE role = :role ORDER BY name');
        $stmt->execute([':role' => $role]);
        return $stmt->fetchAll();
    }

    public function all(): array
    {
        return $this->db->query('SELECT id, name, email, role, address, latitude, longitude, created_at FROM users ORDER BY id DESC')->fetchAll();
    }

    public function updateRole(int $id, string $role): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET role = :role WHERE id = :id');
        return $stmt->execute([':role' => $role, ':id' => $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
