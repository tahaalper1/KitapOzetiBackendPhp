<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // POST isteğiyle gelen verileri al
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'] ?? "";
    $username = $data['username'] ?? "";
    $password = $data['password'] ?? "";

    // Gelen verileri kontrol et
    echo "Gelen POST verileri: <br>";
    var_dump($data);
    echo "<br><br>";

    // Veritabanı bağlantısı
    $servername = "book-db.mysql.database.azure.com";
    $db_username = "taha";
    $db_password = "Ta_12831283";
    $database = "book_database";

    // MySQL bağlantısı oluşturma
    $conn = new mysqli($servername, $db_username, $db_password, $database);

    // Bağlantıyı kontrol etme
    if ($conn->connect_error) {
        die("Bağlantı hatası: " . $conn->connect_error);
    }

    // Şifreyi hashleme
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Kullanıcıyı veritabanına ekleme
    $sql = "INSERT INTO users (email, username, password) VALUES ('$email', '$username', '$hashedPassword')";
    if ($conn->query($sql) === TRUE) {
        echo "Yeni kullanıcı başarıyla oluşturuldu.";
        echo "Email: " . $email . "<br>";
        echo "Username: " . $username . "<br>";
        // Şifre hashlenmiş olduğu için kullanıcıya geri döndürme
    } else {
        echo "Hata: " . $sql . "<br>" . $conn->error;
    }

    // Bağlantıyı kapat
    $conn->close();
}
?>
