<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // POST isteğiyle gelen verileri al
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'] ?? "";
    $password = $data['password'] ?? "";

    // Veritabanı bağlantısı
    $servername = "book-db.mysql.database.azure.com";
    $db_username = "taha";
    $db_password = "Ta_12831283";
    $database = "book_database";

    // MySQL bağlantısı oluşturma
    $conn = new mysqli($servername, $db_username, $db_password, $database);

    // Bağlantıyı kontrol etme ve loglama
    if ($conn->connect_error) {
        $error_msg = "Bağlantı hatası: " . $conn->connect_error;
        error_log($error_msg); // Bağlantı hatasını logla
        die($error_msg);
    } else {
        error_log("Veritabanı bağlantısı başarılı.");
    }

    // Kullanıcı giriş işlemini logla
    error_log("Kullanıcı giriş isteği: Email - " . $email . ", Şifre - " . $password);

    // Kullanıcıyı veritabanından sorgulama
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Veritabanı sorgusunu logla
    error_log("SQL sorgusu: " . $sql);

    if ($result->num_rows > 0) {
        // Kullanıcı bulundu, şifre doğrulama yap
        $row = $result->fetch_assoc();
        $storedPassword = $row['password']; // Veritabanından alınan hashlenmiş şifre
        if (password_verify($password, $storedPassword)) {
            // Şifre doğru, giriş yap
            echo "Success";

            // Oturumu sonlandır
            session_start();
            session_destroy();
        } else {
            // Şifre yanlış
            echo "Error";
        }
    } else {
        // Kullanıcı bulunamadı
        echo "Error";
    }

    // Bağlantıyı kapat ve logla
    $stmt->close();
    $conn->close();
    error_log("Veritabanı bağlantısı kapatıldı.");
}
?>
