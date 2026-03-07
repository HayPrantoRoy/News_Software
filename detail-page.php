

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>Sanatan Times</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="" name="keywords">
        <meta content="" name="description">
        <link rel="icon" type="image/x-icon" href="img/short_logo.jpg">

        <!-- Google Web Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@100;600;800&display=swap" rel="stylesheet"> 

        <!-- Icon Font Stylesheet -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

        <!-- Libraries Stylesheet -->
        <link href="lib/animate/animate.min.css" rel="stylesheet">
        <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">



        <!-- Customized Bootstrap Stylesheet -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="css/style.css" rel="stylesheet">
        <link href="css/head.css" rel="stylesheet">
        <style>
            .py-5 {
    padding-top: 0.8rem !important;
    padding-bottom: 3rem !important;
    .my-4 {
    margin-top: 0rem !important;
    margin-bottom: 1rem !important;
}
}
@font-face {
  font-family: italic;
  src: url(font/Thyssen.ttf);
}

@font-face {
  font-family: Roman_times;
  src: url(font/times.ttf);
}
.right-shadow {
    box-shadow: none !important;
}

        </style>
    </head>

    <body>

        <!-- Spinner Start -->
        <div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">
            <div class="spinner-grow text-primary" role="status"></div>
        </div>
        <!-- Spinner End -->


           <header class="primary-header-wrapper">
        <div class="header-top-section">
            <div class="header-content-flex">
                <a href="#" class="brand-logo-container">
                    <img src="logo.jpg" alt="News Portal Logo" class="logo-image-element">
                </a>
                
                <div class="header-action-group">
                    <div class="search-wrapper-container">
                        <div class="search-input-box">
                            <svg class="search-icon-svg" viewBox="0 0 24 24">
                                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                            </svg>
                            <input type="text" placeholder="Search news..." class="search-field-input">
                        </div>
                    </div>
                    
                    <div class="date-info-display">
                        <svg class="date-icon-svg" viewBox="0 0 24 24">
                            <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                        </svg>
                        <span id="currentDateElement">Today</span>
                    </div>
                    
                    <button class="header-button-element language-btn">
                        <img src="img/united-kingdom.png" width="20px">
                        Eng
                    </button>
                    
                    <button class="header-button-element login-btn">
                        <img src="img/user.png" width="20px">
                        Login
                    </button>
                    
                    <div class="mobile-actions-group">
                        <button class="header-button-element" onclick="toggleMobileSearch()">
                            <svg viewBox="0 0 24 24">
                                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                            </svg>
                        </button>
                        <button class="header-button-element">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 7.5V9C15 12.18 12.18 15 9 15S3 12.18 3 9V7L9 6.5V9C9 10.66 10.34 12 12 12S15 10.66 15 9Z"/>
                            </svg>
                        </button>
                    </div>
                    
                    <button class="mobile-menu-button" onclick="openSidebarPanel()">
                        <svg viewBox="0 0 24 24">
                            <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <nav class="main-navigation-section">
  <div class="nav-content-container">
    <ul class="primary-nav-menu">
      <li class="nav-menu-item"><a href="index.php" class="nav-link-element">Home</a></li>

      <?php
        include "connection.php"; // or write your DB connection code here

        $sql = "SELECT * FROM category ORDER BY id ASC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
            $category_id = $row["id"];
            $category_name = $row["name"];
            echo "<li class='nav-menu-item'><a href='index.php?active_category=$category_id' class='nav-link-element'>$category_name</a></li>";
          }
        }
      ?>
    </ul>

    <div class="nav-actions-container">
      <button class="header-button-element language-btn">
        <img src="img/united-kingdom.png" width="20px">
        Eng
      </button>
    </div>
  </div>
</nav>

    </header>

    <!-- Mobile Sidebar -->
    <div class="sidebar-overlay-bg" id="sidebarOverlayBg" onclick="closeSidebarPanel()"></div>
    <div class="mobile-sidebar-panel" id="mobileSidebarPanel">
        <div class="sidebar-header-container">
            <div class="sidebar-title-text">Menu</div>
            <button class="sidebar-close-btn" onclick="closeSidebarPanel()">
                <svg viewBox="0 0 24 24">
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                </svg>
            </button>
        </div>
        <div class="sidebar-menu-container">
            <a href="#" class="sidebar-menu-link">Home</a>
            <a href="#" class="sidebar-menu-link">Politics</a>
            <a href="#" class="sidebar-menu-link">Bangladesh</a>
            <a href="#" class="sidebar-menu-link">International</a>
            <a href="#" class="sidebar-menu-link">Business</a>
            <a href="#" class="sidebar-menu-link">Sports</a>
            <a href="#" class="sidebar-menu-link">Entertainment</a>
            <a href="#" class="sidebar-menu-link">Technology</a>
            <a href="#" class="sidebar-menu-link">Opinion</a>
            <a href="#" class="sidebar-menu-link">Lifestyle</a>
            <a href="#" class="sidebar-menu-link">Health</a>
            <a href="#" class="sidebar-menu-link">Science</a>
            <a href="#" class="sidebar-menu-link">Education</a>
            <a href="#" class="sidebar-menu-link">Culture</a>
            <a href="#" class="sidebar-menu-link">Travel</a>
            <a href="#" class="sidebar-menu-link">Food</a>
            <a href="#" class="sidebar-menu-link">Weather</a>
            <a href="#" class="sidebar-menu-link">Economy</a>
            <a href="#" class="sidebar-menu-link">Finance</a>
            <a href="#" class="sidebar-menu-link">Markets</a>
            <a href="#" class="sidebar-menu-link">World News</a>
            <a href="#" class="sidebar-menu-link">Local News</a>
            <a href="#" class="sidebar-menu-link">Breaking News</a>
            <a href="#" class="sidebar-menu-link">Photo Gallery</a>
            <a href="#" class="sidebar-menu-link">Videos</a>
            <a href="#" class="sidebar-menu-link">Podcasts</a>
            <a href="#" class="sidebar-menu-link">Newsletter</a>
            <a href="#" class="sidebar-menu-link">Contact Us</a>
            <a href="#" class="sidebar-menu-link">About</a>
            <a href="#" class="sidebar-menu-link">Privacy Policy</a>
        </div>
    </div>
        <!-- Navbar End -->


        <!-- Modal Search Start -->
        <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content rounded-0">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Search by keyword</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body d-flex align-items-center">
                        <div class="input-group w-75 mx-auto d-flex">
                            <input type="search" class="form-control p-3" placeholder="keywords" aria-describedby="search-icon-1">
                            <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Search End -->


        <!-- Single Product Start -->
        <div class="container-fluid py-5">
            <div class="container py-5">
                <ol class="breadcrumb justify-content-start mb-4">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Pages</a></li>
                    <li class="breadcrumb-item active text-dark">Single Page</li>
                </ol>
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="mb-4">
                            <h2>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</h2>
                            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                        </div>
                        <!-- Heading and Read Time -->
<div style="border-left: 3px solid #e63946; padding-left: 10px; margin-bottom: 12px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 400px;">

  <h2 style="font-size: 25px; font-weight: 600; margin: 0 0 6px 0; color: #1d1f20; line-height: 1.1;">
    International Desk
  </h2>

  <div style="display: flex; align-items: center; gap: 12px; font-size: 13px; color: #555;">

    <!-- Reporter -->
    <div style="display: flex; align-items: center; gap: 6px; white-space: nowrap;">
      <img src="img/commentator.png" alt="Reporter Icon" style="width: 22px; height: 22px; border-radius: 50%; object-fit: cover;">
      <span style="font-weight: 600;">Reported By Pranto Roy</span>
    </div>

    <!-- Separator dot -->

    <!-- Published date with clock icon -->
    <div style="display: flex; align-items: center; gap: 4px; white-space: nowrap; font-weight: 500; color: #666;">
  <img src="img/3d-stopwatch.png" alt="Icon" style="height: 15px; width: 15px; object-fit: contain;" />
  <span>30 min ago &bull; 23 June 2025</span>
</div>


  </div>

</div>






<div style="position: relative; border-radius: 8px; overflow: hidden; margin-bottom: 15px;">
    <!-- Image with white shadow at bottom -->
    <div style="position: relative;">
        <img src="img/BatuCaves.jpg" alt="News" style="width: 100%; height: auto; display: block; border-radius: 8px;">

        <!-- White shadow over the image (bottom fade-up) -->
        <div style="
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 40%;
             background: linear-gradient(to top, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0.0) 100%);
            pointer-events: none;
        "></div>

        <!-- Logo + Text on top of image, left-aligned -->
        <div style="
            position: absolute;
            bottom: 15px;
            left: 20px;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            color: #111;
        ">
            <img src="img/white_logo.png" alt="Logo" style="width: 140px;">

            <span style="font-size: 16px; font-weight: bold; color: white;">Hindu temple</span>
            <span style="
                margin-top: 4px;
                width: 40px;
                height: 3px;
                background: rgb(116, 215, 243);
                border-radius: 2px;
            "></span>
        </div>
    </div>
</div>








<!-- Share Icons -->
<div style="display: flex; align-items: center; gap: 0px;">
    <!-- "Share on" text -->
    <span style="font-size: 16px; ">Share on : </span>

    <!-- Social Media Icons (images, no border-radius) -->
    <a href="#" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
        <img src="img/facebook.png" alt="Facebook" style="width: 24px; height: 24px;">
    </a>
    <a href="#" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
        <img src="img/pdf.png" alt="Link" style="width: 24px; height: 24px;">
    </a>
    <a href="#" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
        <img src="img/copy.png" alt="Link" style="width: 24px; height: 24px;">
    </a>
    <a href="#" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
        <img src="img/printer.png" alt="Link" style="width: 24px; height: 24px;">
    </a>
    <a href="#" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
        <img src="img/next.png" alt="Share" style="width: 24px; height: 24px;">
    </a>
</div>



                        
<p style="
    text-align: justify;
    margin: 20px 0;
">
    <span style="
        float: left;
        font-size: 48px;
        line-height: 1;
        font-weight: bold;
        color: #000;
        margin-right: 12px;
        font-family: 'Georgia', 'Times New Roman', serif;
    ">L</span>
    orem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. 
    It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets.
</p>




                        <p class="my-4">
                            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic
                            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic
                            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic
                        </p>
                        <div class="bg-light p-4 mb-4 rounded border-start border-5 border-primary shadow-sm" style=" position: relative;">
  <div style="position: absolute; left: 1rem; top: 1.2rem; font-size: 2.5rem; color: #0d6efd; font-weight: bold; line-height: 1;">
    &#8220;
  </div>
  <h1 class="mb-0 ps-4 " style="color: black; font-weight: 500; font-size: 1.8rem; font-family: 'Fredoka', sans-serif;"> 
    Lorem Ipsum is simply dummy text of the printing and typesetting industry.
  </h1>
  <div class="text-end mt-3" style="font-style: normal; font-weight: 600; color: #6c757d; font-size: 1rem;">
    — Famous Saying
  </div>
</div>

                        <div class="row g-4">
                            <div class="col-6">
                                <div class="rounded overflow-hidden">
                                    <img src="img/news-6.jpg" class="img-zoomin img-fluid rounded w-100" alt="">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="rounded overflow-hidden">
                                    <img src="img/news-5.jpg" class="img-zoomin img-fluid rounded w-100" alt="">
                                </div>
                            </div>
                            
                        </div>
                        <p class="my-4">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy Lorem Ipsum has been the industry's standard dummy type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic
                            Lorem Ipsum is simply dummy
                        </p>
                        <p class="my-4">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy Lorem Ipsum has been the industry's standard dummy type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic
                            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been ther took It has survived not only five centuries, but also the leap into electronic
                        </p>
                        
                        <p class="my-4">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy Lorem Ipsum has been the industry's standard dummy type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic
                            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been ther took It has survived not only five centuries, but also the leap into electronic
                        </p>
                        
                        <div class="bg-light rounded my-4 p-4">
    <h4 class="mb-4">You Might Also Like</h4>
    <div class="row g-4">
        <?php
        $related_query = "SELECT * FROM news WHERE is_active = 1 ORDER BY id ASC LIMIT 10";
        $related_result = mysqli_query($conn, $related_query);

        while ($related = mysqli_fetch_assoc($related_result)):
            $related_date = date('d F Y', strtotime($related['created_at']));
            $related_image = !empty($related['image_url']) ? $related['image_url'] : 'img/default.jpg';
        ?>
        <div class="col-lg-6">
            <div class="d-flex align-items-center p-3 bg-white rounded">
                <img src="img/<?php echo htmlspecialchars($related_image); ?>" class="img-fluid rounded" alt="" style="width: 100px; height: auto;">
                <div class="ms-3">
                    <a href="news-details.php?id=<?php echo $related['id']; ?>" class="h6 mb-2">
                        <?php echo htmlspecialchars($related['headline']); ?>
                    </a>
                    <p class="text-dark mt-3 mb-0 me-3">
                        <i class="fa fa-clock"></i> <?php echo $related_date; ?>
                    </p>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

                        
                        
                    </div>
                    <div class="col-lg-4">
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="p-3 rounded border">
                                    
                                    
                                    <h4 class="my-4">Popular News</h4>
<div class="row g-4">
    <?php
    include 'connection.php';
    date_default_timezone_set('Asia/Dhaka');

    // সর্বশেষ ৪টি সক্রিয় নিউজ (নতুন প্রথমে)
    $popular_news_query = "SELECT n.*, c.name as category_name 
                           FROM news n 
                           JOIN category c ON n.category_id = c.id 
                           WHERE n.is_active = 1 
                           ORDER BY n.created_at DESC 
                           LIMIT 20";

    $popular_news_result = mysqli_query($conn, $popular_news_query);

    while ($news = mysqli_fetch_assoc($popular_news_result)):
        $formatted_date = date('F j, Y', strtotime($news['created_at']));
        $image = !empty($news['image_url']) ? $news['image_url'] : 'img/default.jpg';
    ?>
    <div class="col-12">
        <div class="row g-4 align-items-center features-item">
            <div class="col-4">
                <div class="position-relative">
                    <div class="overflow-hidden">
                        <img src="img/<?php echo htmlspecialchars($image); ?>" class="img-zoomin img-fluid w-100" alt="">
                    </div>
                </div>
            </div>
            <div class="col-8">
                <div class="features-content d-flex flex-column">
                    <p class="text-uppercase mb-2"><?php echo htmlspecialchars($news['category_name']); ?></p>
                    <a href="news-details.php?id=<?php echo $news['id']; ?>" class="h6">
                        <?php echo htmlspecialchars($news['headline']); ?>
                    </a>
                    <small class="text-body d-block">
                        <i class="fas fa-calendar-alt me-1"></i> <?php echo $formatted_date; ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Single Product End -->


         <!-- Footer Start -->
        <div class="container-fluid bg-dark footer py-5">
            <div class="container py-5">
                <div class="pb-4 mb-4" style="border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                    
                </div>
                <div class="row g-5">
                    <div class="col-lg-6 col-xl-3">
                        <div class="footer-item-1">
                            <h4 class="mb-4 text-white">Get In Touch</h4>
                            <p class="text-secondary "><span class="text-white">For any information or to know about us, please email contact@sanatantimes.news
We're here to answer your questions.
Stay updated with the latest from Sanatan Times.</span></p>
                            
                        </div>
                    </div>
                    <div class="col-lg-6 col-xl-3">
                        <div class="footer-item-2">
    <?php
    include 'connection.php';

    $sql = "SELECT * FROM news WHERE is_active = 1 ORDER BY created_at DESC LIMIT 2";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $headline = $row['headline'];
            $image = $row['image_url'];
            $date = date("M j, Y", strtotime($row['created_at']));
            echo '
            <div class="d-flex flex-column mb-4">
                <a href="#">
                    <div class="d-flex align-items-center">
                        <div class="border border-2 border-primary overflow-hidden" style="width: 60px; height: 60px;">
                            <img src="img/' . $image . '" class="img-zoomin img-fluid  w-100 h-100" alt="">
                        </div>
                        <div class="d-flex flex-column ps-4">
                            <a href="#" class="h6 text-white">' . $headline . '</a>
                            <small class="text-white d-block"><i class="fas fa-calendar-alt me-1"></i> ' . $date . '</small>
                        </div>
                    </div>
                </a>
            </div>';
        }
    }
    ?>
</div>

                    </div>
                    <div class="col-lg-6 col-xl-3">
    <div class="d-flex flex-column text-start footer-item-3">
        <h4 class="mb-4 text-white">Categories</h4>
        <?php
        include 'connection.php';

        $sql = "SELECT * FROM category ORDER BY id ASC LIMIT 6";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $cat_name = $row['name'];
                echo '
                <a class="btn-link text-white" href="#">
                    <i class="fas fa-angle-right text-white me-2"></i> ' . htmlspecialchars($cat_name) . '
                </a>';
            }
        }
        ?>
    </div>
</div>

                    <div class="col-lg-6 col-xl-3">
                        <div class="footer-item-4">
                            <h4 class="mb-4 text-white">Our Gallary</h4>
                            <div class="row g-2">
                                <div class="col-4">
                                    <div class="rounded overflow-hidden">
                                        <img src="img/Akshardham.jpg" class="img-zoomin img-fluid rounded w-100" alt="">
                                    </div>
                               </div>
                               <div class="col-4">
                                    <div class="rounded overflow-hidden">
                                        <img src="img/Danville.jpg" class="img-zoomin img-fluid rounded w-100" alt="">
                                    </div>
                               </div>
                                <div class="col-4">
                                    <div class="rounded overflow-hidden">
                                        <img src="img/Concord.jpg" class="img-zoomin img-fluid rounded w-100" alt="">
                                    </div>
                               </div>
                                <div class="col-4">
                                    <div class="rounded overflow-hidden">
                                        <img src="img/baps-south-africa.jpg" class="img-zoomin img-fluid rounded w-100" alt="">
                                    </div>
                               </div>
                                <div class="col-4">
                                    <div class="rounded overflow-hidden">
                                        <img src="img/kantajiu.jpg" class="img-zoomin img-fluid rounded w-100" alt="">
                                    </div>
                               </div>
                               <div class="col-4">
                                <div class="rounded overflow-hidden">
                                    <img src="img/Shri-Sanathana-Dharma-Aalayam.jpg" class="img-zoomin img-fluid rounded w-100" alt="">
                                </div>
                           </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer End -->


        <!-- Copyright Start -->
        <div class="container-fluid copyright bg-dark py-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <span class="text-light"><i class="fas fa-copyright text-light me-2"></i>Sanatan Times, All right reserved.</span>
                    </div>
                    
                </div>
            </div>
        </div>
        <!-- Copyright End -->
         


        <!-- Back to Top -->
        <a href="#" class="btn btn-primary border-2 border-white rounded-circle back-to-top"><i class="fa fa-arrow-up"></i></a>   

        
        <!-- JavaScript Libraries -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="lib/easing/easing.min.js"></script>
        <script src="lib/waypoints/waypoints.min.js"></script>
        <script src="lib/owlcarousel/owl.carousel.min.js"></script>

        <!-- Template Javascript -->
        <script src="js/main.js"></script>
    </body>

</html>