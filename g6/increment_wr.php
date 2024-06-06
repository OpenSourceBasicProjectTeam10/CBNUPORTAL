<?php
header('Access-Control-Allow-Origin: *'); // 모든 도메인에서의 요청 허용
header('Access-Control-Allow-Methods: POST, GET, OPTIONS'); // 허용할 HTTP 메소드
header('Access-Control-Allow-Headers: Content-Type'); // 허용할 HTTP 헤더
header('Content-Type: application/json');

// 데이터베이스 연결 설정
$servername = "127.0.0.1";
$username = "cbnuopenproject";
$password = "open1234";
$dbname = "cbnuopenproject";

// 데이터베이스 연결
$conn = new mysqli($servername, $username, $password, $dbname);

// 연결 확인
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// POST 데이터 확인
if(isset($_POST['post_id'])) {
    $postId = $_POST['post_id'];

    // SQL 쿼리를 통해 wr_1 값을 1 증가시키기
    $sql = "UPDATE g6_CBNUPORTALwrite_contest SET wr_1 = wr_1 + 1 WHERE wr_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $postId);
    
    if($stmt->execute()) {
        echo json_encode(array('success' => true, 'message' => 'wr_1 값이 1 증가했습니다.'));
    } else {
        echo json_encode(array('success' => false, 'error' => '데이터베이스 업데이트 실패: ' . $stmt->error));
    }
    
    $stmt->close();
} else {
    // POST 데이터가 없는 경우
    echo json_encode(array('success' => false, 'error' => '게시글 ID가 전송되지 않았습니다.'));
}

// 데이터베이스 연결 종료
$conn->close();
?>
