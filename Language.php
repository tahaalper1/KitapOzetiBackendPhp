<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // POST isteğiyle gelen verileri al
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'] ?? "";
    $password = $data['password'] ?? "";
    $language = $data['language'] ?? ""; // Yeni dil seçeneği

    // Veritabanı bağlantısı
    $servername = "localhost";
    $username = "username";
    $password = "password";
    $dbname = "database";

    // MySQL bağlantısı oluşturma
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Bağlantıyı kontrol etme
    if ($conn->connect_error) {
        die("Veritabanı bağlantı hatası: " . $conn->connect_error);
    }

    // Kullanıcıyı veritabanından sorgulama
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Kullanıcı bulundu, şifre doğrulama yap
        $row = $result->fetch_assoc();
        $storedPassword = $row['password']; // Veritabanından alınan hashlenmiş şifre
        if (password_verify($password, $storedPassword)) {
            // Şifre doğru, dil bilgisini güncelle
            $updateSql = "UPDATE users SET language = ? WHERE email = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("ss", $language, $email);
            $updateResult = $updateStmt->execute();

            if ($updateResult) {
                // Dil başarıyla güncellendi
                echo "Success";
            } else {
                // Dil güncellenemedi
                echo "Error";
            }
        } else {
            // Şifre yanlış
            echo "PasswordError";
        }
    } else {
        // Kullanıcı bulunamadı
        echo "UserNotFound";
    }

    // Bağlantıyı kapat
    $stmt->close();
    $conn->close();
}
?>
