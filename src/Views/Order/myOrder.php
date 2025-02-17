<?php if (isset($_SESSION['success'])): ?>
        <p class="success-message"><?= htmlspecialchars($_SESSION['success']) ?></p>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
        <p class="error"><?= htmlspecialchars($_SESSION['error']) ?></p>
<?php endif; ?>

<h1>Mis Pedidos</h1>

<?php if (isset($pedidos) && !empty($pedidos)): ?>
    <table class="orders-table">
        <thead>
            <tr>
                <th>ID Pedido</th>
                <?php if ($_SESSION['identity']['rol'] == $_ENV['ADMIN']): ?>
                    <th>ID Cliente</th>
                <?php endif; ?>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Total</th>
                <?php if ($_SESSION['identity']['rol'] == $_ENV['ADMIN']): ?>
                    <th>Acción</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pedidos as $pedido): ?>
                <tr>
                    <td><?= htmlspecialchars($pedido['id']) ?></td>
                    <?php if ($_SESSION['identity']['rol'] == $_ENV['ADMIN']): ?>
                        <td><?= htmlspecialchars($pedido['usuario_id']) ?></td>
                    <?php endif; ?>
                    <td><?= htmlspecialchars($pedido['fecha']) ?></td>
                    <td><?= htmlspecialchars($pedido['estado']) ?></td>
                    <td><?= htmlspecialchars($pedido['coste']) ?>€</td>
                    <?php if ($_SESSION['identity']['rol'] == $_ENV['ADMIN']): ?>
                        <td>
                            <form action="<?= BASE_URL ?>orders/updateOrderState" method="POST">
                                <select name="estado">
                                    <option value="Pendiente" <?= ($pedido['estado'] == 'Pendiente') ? 'selected' : '' ?>>Pendiente
                                    </option>
                                    <option value="Procesando" <?= ($pedido['estado'] == 'Procesando') ? 'selected' : '' ?>>Procesando
                                    </option>
                                    <option value="Enviado" <?= ($pedido['estado'] == 'Enviado') ? 'selected' : '' ?>>Enviado</option>
                                    <option value="Completado" <?= ($pedido['estado'] == 'Completado') ? 'selected' : '' ?>>Completado
                                    </option>
                                    <option value="Cancelado" <?= ($pedido['estado'] == 'Cancelado') ? 'selected' : '' ?>>Cancelado
                                    </option>
                                </select>
                                <input type="hidden" name="order_id" value="<?= htmlspecialchars($pedido['id']) ?>" />
                                <button type="submit" class="submit-button">Update</button>
                            </form>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No tienes pedidos.</p>
<?php endif; ?>


<?php
// Limpiar mensajes de sesión después de mostrarlos
unset($_SESSION['message']);
unset($_SESSION['error']);
?>
