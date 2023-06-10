<!DOCTYPE html>
<html>

<head>
    <title>Data Management</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable
            $('#data-table').DataTable();

            // Konfigurasi datepicker
            $('#datepicker').datepicker({
                dateFormat: 'yy-mm-dd'
            });

            // Validasi form tambah project
            $('#add-project-form').validate({
                rules: {
                    name: 'required',
                    description: 'required',
                    manager: 'required',
                    status: 'required'
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });

            // Validasi form edit project
            $('#edit-project-form').validate({
                rules: {
                    name: 'required',
                    description: 'required',
                    manager: 'required',
                    status: 'required'
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });

            // Tampilkan dialog edit project saat tombol di klik
            $('.edit-project-btn').click(function() {
                var projectId = $(this).data('project-id');
                var projectRow = $(this).closest('tr');
                var name = projectRow.find('.name').text();
                var description = projectRow.find('.description').text();
                var manager = projectRow.find('.manager').text();
                var status = projectRow.find('.status').text();

                $('#edit-project-form').attr('action', 'http://localhost:3000/projects/' + projectId);
                $('#edit-name').val(name);
                $('#edit-description').val(description);
                $('#edit-manager').val(manager);
                $('#edit-status').val(status);

                $('#edit-project-dialog').dialog('open');
            });

            // Menghapus project saat tombol di klik
            $('.delete-project-btn').click(function() {
                var projectId = $(this).data('project-id');

                if (confirm("Apakah Anda yakin ingin menghapus project ini?")) {
                    $.ajax({
                        url: 'http://localhost:3000/projects/' + projectId,
                        type: 'DELETE',
                        success: function(response) {
                            alert('Project berhasil dihapus!');
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            console.log(xhr.responseText);
                            alert('Terjadi kesalahan saat menghapus project. Silakan coba lagi.');
                        }
                    });
                }
            });

            // Konfigurasi dialog edit project
            $('#edit-project-dialog').dialog({
                autoOpen: false,
                buttons: {
                    "Update": function() {
                        var projectId = $('#edit-project-form').attr('action');
                        var formData = {
                            name: $('#edit-name').val(),
                            description: $('#edit-description').val(),
                            manager: $('#edit-manager').val(),
                            status: $('#edit-status').val()
                        };

                        $.ajax({
                            url: projectId,
                            type: 'PATCH',
                            data: JSON.stringify(formData),
                            contentType: 'application/json',
                            success: function(response) {
                                alert('Project berhasil diperbarui!');
                                location.reload();
                            },
                            error: function(xhr, status, error) {
                                console.log(xhr.responseText);
                                alert('Terjadi kesalahan saat memperbarui project. Silakan coba lagi.');
                            }
                        });

                        $(this).dialog('close');
                    },
                    "Cancel": function() {
                        $(this).dialog('close');
                    }
                }
            });
        });
    </script>
    <style>
        .ui-datepicker {
            font-size: 12px;
        }
    </style>
</head>

<body>
    <h1>Data Management</h1>

    <!-- Form Tambah Project -->
    <h2>Tambah Project</h2>
    <form id="add-project-form" method="POST" action="http://localhost:3000/projects">
        <label for="name">Nama:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="description">Deskripsi:</label>
        <input type="text" id="description" name="description" required><br><br>

        <label for="manager">Manajer:</label>
        <input type="text" id="manager" name="manager" required><br><br>

        <label for="status">Status:</label>
        <input type="text" id="status" name="status" required><br><br>

        <input type="submit" value="Submit">
    </form>

    <!-- Tabel Data -->
    <h2>Proyek</h2>
    <table id="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Deskripsi</th>
                <th>Manajer</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $curl = curl_init('http://localhost:3000/projects');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);
            curl_close($curl);

            $projects = json_decode($response, true);

            foreach ($projects as $project) {
                echo '<tr>';
                echo '<td>' . $project['id'] . '</td>';
                echo '<td class="name">' . $project['name'] . '</td>';
                echo '<td class="description">' . $project['description'] . '</td>';
                echo '<td class="manager">' . $project['manager'] . '</td>';
                echo '<td class="status">' . $project['status'] . '</td>';
                echo '<td>';
                echo '<button class="edit-project-btn" data-project-id="' . $project['id'] . '">Update</button>';
                echo '<button class="delete-project-btn" data-project-id="' . $project['id'] . '">Delete</button>';
                echo '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>

    <!-- Dialog Edit Project -->
    <div id="edit-project-dialog" title="Edit Project">
        <form id="edit-project-form" method="POST" action="">
            <label for="edit-name">Nama:</label><br>
            <input type="text" id="edit-name" name="name" required><br><br>

            <label for="edit-description">Deskripsi:</label><br>
            <input type="text" id="edit-description" name="description" required><br><br>

            <label for="edit-manager">Manajer:</label><br>
            <input type="text" id="edit-manager" name="manager" required><br><br>

            <label for="edit-status">Status:</label><br>
            <input type="text" id="edit-status" name="status" required><br><br>

            <button id="update-project-btn">Update</button>
            <button id="cancel-project-btn">Cancel</button>
        </form>
    </div>
</body>

</html>