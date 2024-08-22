<?php
    $apiKey = 'API_KEY';
    $language = 'ko-KR';
    $url = 'https://api.themoviedb.org/3/movie/popular?api_key='.$apiKey.'&language='.$language;

    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // API 호출
    $response = curl_exec($ch);

    // cURL 세션 종료
    curl_close($ch);

    // JSON 응답을 PHP 배열로 변환
    $movieData = json_decode($response, true);

    $movies = $movieData['results'];

    // 인기 영화 backdrop_path를 배열로 저장
    $backdropUrls = [];
    foreach ($movies as $movie) {
        $backdropUrls[] = 'https://image.tmdb.org/t/p/original' . $movie['backdrop_path'];
    }
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="css/reset.css" rel="stylesheet" />
    <link href="css/index.css" rel="stylesheet" />

    <!-- swiper cdn -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <title>JJINIS BOX</title>
</head>
<body>
    <?php require_once 'header.php'; ?>
    <section id="dynamicBackground">
        <div class="back-cover"></div>
        <h2>JJINIS BOX 함께<br>다양한 영화를 즐겨보세요</h2>
        <a href="#" class="add-movie-btn">더 많은 영화보기 +</a>
        <div class="popular-list">

            <p>포스터를 클릭해 상세내용을 확인해 보세요</p>
            <div class="swiper popularListSwiper">
                <div class="swiper-wrapper">
                <?php 
                    foreach ($movies as $movie) { // 인기 영화 리스트 가져오기
                ?>
                    <div class="swiper-slide">
                        <div class="overview-content"></div>
                        <img src="https://image.tmdb.org/t/p/w300<?= $movie['poster_path'] ?>" alt="인기 영화 포스터"/>
                    </div>
                <?php      
                    }
                ?>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </section>

    <script>
        // 인기 영화 포스터 swiper
        var swiper = new Swiper(".popularListSwiper", {
            slidesPerView: 6,
            loop: true,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            autoplay : {  
                delay : 3800,   // 시간 설정
                disableOnInteraction: false
            },
        });

        // PHP에서 전달된 이미지 URL 배열을 JavaScript 배열로 변환
        const backdropUrls = <?= json_encode($backdropUrls) ?>;
        let currentIndex = 0;

        // 배경이미지 모두 로드하는 함수
        function preloadImages(urls, callback) {
            let loadedCount = 0;
            const totalCount = urls.length;
            const images = [];

            for (let i = 0; i < totalCount; i++) {
                images[i] = new Image();
                images[i].src = urls[i];
                images[i].onload = function () {
                    loadedCount++;
                    if (loadedCount === totalCount) {
                        callback();
                    }
                };
            }
        }

        // 배경 이미지 변경 함수
        function changeBackgroundImage() {
            const section = document.getElementById('dynamicBackground');
            section.style.backgroundImage = `url(${backdropUrls[currentIndex]})`;
            currentIndex = (currentIndex + 1) % backdropUrls.length;
        }

        // 모든 이미지를 미리 로드한 후 배경 이미지 변경 시작
        preloadImages(backdropUrls, function() {
            // 초기 배경 이미지 설정
            changeBackgroundImage();

            // 5초마다 배경 이미지 변경
            setInterval(changeBackgroundImage, 3800);
        });
    </script>
</body>
</html>
