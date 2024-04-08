<?php
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

// POST isteğiyle gelen verileri al
$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);
$email = $data['email'] ?? "";
$password = $data['password'] ?? "";
$bookName = $data['bookName'] ?? "";
$summary = $data['summary'] ?? "";
$imageURL = $data['imageURL'] ?? "";

// Kullanıcı giriş bilgilerini kontrol et
$sql_user = "SELECT * FROM users WHERE email = '$email'";
$result_user = $conn->query($sql_user);

if ($result_user->num_rows > 0) {
    // Kullanıcı bulundu
    $row_user = $result_user->fetch_assoc();
    $hashed_password = $row_user['password'];
    if (password_verify($password, $hashed_password)) {
        // Şifre doğru, kitap bilgilerini kaydet
        $userId = $row_user['id']; // Kullanıcının benzersiz kimliği
        $sql_insert = "INSERT INTO books (user_id, book_name, book_summary, image_url) VALUES ('$userId', '$bookName', '$summary', '$imageURL')";
        if ($conn->query($sql_insert) === TRUE) {
            // Kitap başarıyla kaydedildi
            echo json_encode("Success");
        } else {
            // Kitap kaydedilemedi
            echo json_encode("Error: " . $conn->error);
        }
    } else {
        // Şifre yanlış
        echo json_encode("Error: Invalid credentials");
    }
} else {
    // Kullanıcı bulunamadı
    echo json_encode("Error: Invalid credentials");
}

// Bağlantıyı kapat ve logla
$conn->close();
error_log("Veritabanı bağlantısı kapatıldı.");
?>
