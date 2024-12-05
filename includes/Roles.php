<?php
function tienePermiso($pdo, $usuario_id, $permiso_nombre) {
    $stmt = $pdo->prepare('
        SELECT p.nombre
        FROM users u
        JOIN roles r ON u.rol = r.id
        JOIN roles_permisos rp ON r.id = rp.rol_id
        JOIN permisos p ON rp.permiso_id = p.id
        WHERE u.id = ? AND p.nombre = ?
    ');
    $stmt->execute([$usuario_id, $permiso_nombre]);
    return $stmt->fetchColumn() !== false;
}
?>
