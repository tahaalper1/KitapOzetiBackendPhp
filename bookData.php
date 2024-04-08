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

// Kullanıcı giriş bilgilerini kontrol et
$sql_user = "SELECT * FROM users WHERE email = '$email'";
$result_user = $conn->query($sql_user);

if ($result_user->num_rows > 0) {
    // Kullanıcı bulundu
    $row_user = $result_user->fetch_assoc();
    $hashed_password = $row_user['password'];
    if (password_verify($password, $hashed_password)) {
        // Şifre doğru, kullanıcının kitaplarını al
        $user_id = $row_user['id']; // Kullanıcının benzersiz kimliği
        $sql_books = "SELECT * FROM books WHERE user_id = '$user_id'";
        $result_books = $conn->query($sql_books);

        // Kitap verilerini JSON formatında döndür
        $books = array();
        if ($result_books->num_rows > 0) {
            while ($row_book = $result_books->fetch_assoc()) {
                $books[] = $row_book;
            }
        }

        echo json_encode($books);
    } else {
        // Şifre yanlış
        echo json_encode("Error: Invalid credentials");
    }
} else {
    // Kullanıcı bulunamadı
    echo json_encode("Error: User not found");
}

// Bağlantıyı kapat ve logla
$conn->close();
error_log("Veritabanı bağlantısı kapatıldı.");
?>
