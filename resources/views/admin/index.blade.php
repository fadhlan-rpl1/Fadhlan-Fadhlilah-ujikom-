<!DOCTYPE html>
<html>
<head><title>Kelola User</title></head>
<body>
    <h2>Manajemen User</h2>
    <a href="/admin/dashboard">Kembali</a>
    <hr>

    <form action="/admin/users" method="POST">
        @csrf
        <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role">
            <option value="petugas">Petugas</option>
            <option value="owner">Owner</option>
            <option value="admin">Admin</option>
        </select>
        <button type="submit">Tambah User</button>
    </form>

    <table border="1" cellpadding="10" style="margin-top:20px;">
        <tr>
            <th>Nama</th>
            <th>Username</th>
            <th>Role</th>
        </tr>
        @foreach($users as $u)
        <tr>
            <td>{{ $u->nama_lengkap }}</td>
            <td>{{ $u->username }}</td>
            <td>{{ $u->role }}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>