<?php
require_once __DIR__ . '/../security.php';

if (!isset($_SESSION['tuvastamine']) || $_SESSION['role'] !== 'administraator') {
  header('Location: login.php');
  exit();
  }

?>

<?php include('../config.php'); ?>
<?php include('../header.php'); ?>

<!-- sisu -->
<div class="container">
    <h2>Kasutajate haldamine</h2>
    <a href="index.php" class="btn btn-secondary mb-3">Tagasi adminni pealehele</a>

    <?php
    // Handle user deletion
    if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
        require_csrf_token($_GET['csrf_token'] ?? null);
        $user_id = (int)$_GET['delete'];

        // Don't allow deleting yourself
        if ($user_id !== $_SESSION['user_id']) {
            $delete_paring = "DELETE FROM users WHERE id = $user_id";
            $result = mysqli_query($yhendus, $delete_paring);

            if ($result) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Kasutaja edukalt kustutatud.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>';
            } else {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Viga kasutaja kustutamisel.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>';
            }
        } else {
            echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                    Ei saa kustutada omaenda kontot.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        }
    }

    // Handle user update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
        require_csrf_token($_POST['csrf_token'] ?? null);
        $user_id = (int)$_POST['user_id'];
        $first_name = mysqli_real_escape_string($yhendus, $_POST['first_name']);
        $last_name = mysqli_real_escape_string($yhendus, $_POST['last_name']);
        $email = mysqli_real_escape_string($yhendus, $_POST['email']);
        $role = mysqli_real_escape_string($yhendus, $_POST['role']);
        if (!in_array($role, ['kasutaja', 'administraator'], true)) {
            $role = 'kasutaja';
        }

        $update_paring = "UPDATE users SET first_name = '$first_name', last_name = '$last_name', email = '$email', role = '$role' WHERE id = $user_id";
        $result = mysqli_query($yhendus, $update_paring);

        if ($result) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    Kasutaja andmed edukalt uuendatud.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        } else {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Viga kasutaja andmete uuendamisel.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        }
    }

    // Get all users
    $users_paring = "SELECT * FROM users ORDER BY id ASC";
    $users_result = mysqli_query($yhendus, $users_paring);
    ?>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Eesnimi</th>
                    <th>Perekonnanimi</th>
                    <th>Email</th>
                    <th>Roll</th>
                    <th>Registreeritud</th>
                    <th>Tegevused</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                    <td><?php echo $user['created_at']; ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick='editUser(<?php echo (int)$user['id']; ?>, <?php echo json_encode($user['first_name']); ?>, <?php echo json_encode($user['last_name']); ?>, <?php echo json_encode($user['email']); ?>, <?php echo json_encode($user['role']); ?>)'>
                            Muuda
                        </button>
                        <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                        <a href="?delete=<?php echo (int)$user['id']; ?>&<?php echo csrf_query(); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Kas oled kindel, et soovid kustutada kasutaja <?php echo e($user['first_name'] . ' ' . $user['last_name']); ?>?')">
                            Kustuta
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Muuda kasutajat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    <div class="mb-3">
                        <label for="edit_first_name" class="form-label">Eesnimi</label>
                        <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_last_name" class="form-label">Perekonnanimi</label>
                        <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_role" class="form-label">Roll</label>
                        <select class="form-select" id="edit_role" name="role" required>
                            <option value="kasutaja">Kasutaja</option>
                            <option value="administraator">Administraator</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tühista</button>
                    <button type="submit" name="update_user" class="btn btn-primary">Salvesta muudatused</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

<script>
function editUser(id, firstName, lastName, email, role) {
    document.getElementById('edit_user_id').value = id;
    document.getElementById('edit_first_name').value = firstName;
    document.getElementById('edit_last_name').value = lastName;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_role').value = role;

    var modal = new bootstrap.Modal(document.getElementById('editUserModal'));
    modal.show();
}
</script>

</body>
</html>
