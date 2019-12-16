<?php
/**
 * Template Name: Audit page v2
 */
?>
<?php 
// Cache busting
include(dirname(__FILE__)."/../../assets/php/cache_version.php");

$leadengine = get_template_directory_uri();

?>
<html>
<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-149815594-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-149815594-1');
  </script>

  <title>Audit</title>
  <!-- TODO: Moet nog met chrome canary worden gecheckt... -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

  <link rel="stylesheet" href="<?php echo $leadengine; ?>/dashboard/assets/styles/audit.css<?php echo $cache_version; ?>" type="text/css">
  <script src="<?php echo $leadengine; ?>/dashboard/assets/scripts/modal.js<?php echo $cache_version; ?>"></script>
  <script src="<?php echo $leadengine; ?>/dashboard/assets/scripts/functions.js<?php echo $cache_version; ?>"></script>

  <script>var ajaxurl = '<?php echo admin_url('admin-ajax.php');?>';</script>

  <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>
<body>
<header>
    <div class="audit-name">Audit name</div>
    <div class="languages">Dutch <i class="fas fa-chevron-down"></i></div>
</header>
<section class="introduction">
    <div class="sidebar">
        <div class="audit-owner vertical-align">
            <div class="profile-picture">
                <img src="https://image.shutterstock.com/image-photo/happy-businessman-isolated-handsome-man-260nw-609414131.jpg" alt="Profile image" />
            </div>
            <span class="name">Random Name</span>
            <div class="contact-icons">
                <a href="#"><i class="fas fa-envelope"></i></a>
                <a href="#"><i class="fas fa-globe"></i></a>
                <a href="#"><i class="fas fa-phone"></i></a>
            </div>
        </div>
    </div>
    <div class="introduction-right">
        <div class="introduction-text">
            <div class="intro-text-block vertical-align">
                <span class="title">Improvements</span>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse. cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                <div class="buttons">
                    <a href="#" class="button">Call Name</a>
                    <a href="#" class="button">Book a meeting</a>
                </div>
            </div>
        </div>
        <div class="video">
            <div class="video-iframe vertical-align">
                <iframe width="560" height="315" src="https://www.youtube.com/embed/unU9vpLjHRk" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</section>
<section id="facebook-section">
    <div class="sidebar">
        <span class="title">Statistics</span>
        <ul>
            <li class="facebook-option active"><i class="fab fa-facebook-square"></i><span class="nav-position">Facebook</span></li>
            <li class="instagram-option"><i class="fab fa-instagram"></i><span class="nav-position">Instagram</span></li>
            <li class="website-option"><i class="fas fa-globe"></i><span class="nav-position">Website</span></li>
            <li clas="conclusion-option"><i class="fas fa-check"></i><span class="nav-position">Conclusion</span></li>
        </ul>
        <a href="#" class="button" style="background: #dbecfd; font-weight: bold; color: #4da1ff; box-shadow: none;">Generate PDF</a>
    </div>
    <div class="facebook-right">
        <i class="fab fa-facebook-square section-icon"></i>
        <span class="section-title">Facebook statistics</span>     
        <span class="section-subtitle">Compare your Facebook page</span>   

        <div class="statistics">
            <div class="stat-box">
                <span class="stat-title">Post each month</span>
                <i class="fas fa-info-circle information"></i>
                <div class="skills" data-percent="95%">
                    <div class="title-bar">
                        <h5>You</h5>
                    </div>
                    <span class="procent font-blue">95</span>
                    <div style="clear: both;"></div>
                    <div class="skillbar blue"></div>  
                </div>
                <div class="skills" data-percent="30%">
                        <div class="title-bar">
                            <h5>NOS</h5>
                        </div>
                        <span class="procent font-red">30</span>
                        <div style="clear: both;"></div>
                        <div class="skillbar red"></div>  
                </div>
                <hr class="x-as" />
                <span class="left-value">10</span>
                <span class="center-value">50</span>
                <span class="right-value">100</span>
            </div>
            <div class="stat-box">
            <span class="stat-title">Videos</span>
                <i class="fas fa-info-circle information"></i>
                <div class="skills" data-percent="80%">
                    <div class="title-bar">
                        <h5>You</h5>
                    </div>
                    <span class="procent font-blue">80</span>
                    <div style="clear: both;"></div>
                    <div class="skillbar blue"></div>  
                </div>
                <div class="skills" data-percent="20">
                        <div class="title-bar">
                            <h5>NOS</h5>
                        </div>
                        <span class="procent font-red">20</span>
                        <div style="clear: both;"></div>
                        <div class="skillbar red"></div>  
                </div>
                <hr class="x-as" />
                <span class="left-value">10</span>
                <span class="center-value">50</span>
                <span class="right-value">100</span>
            </div>
            <div class="stat-box">
            <span class="stat-title">Average post length</span>
                <i class="fas fa-info-circle information"></i>
                <div class="skills" data-percent="60%">
                    <div class="title-bar">
                        <h5>You</h5>
                    </div>
                    <span class="procent font-blue">60</span>
                    <div style="clear: both;"></div>
                    <div class="skillbar blue"></div>  
                </div>
                <div class="skills" data-percent="30%">
                        <div class="title-bar">
                            <h5>NOS</h5>
                        </div>
                        <span class="procent font-red">30</span>
                        <div style="clear: both;"></div>
                        <div class="skillbar red"></div>  
                </div>
                <hr class="x-as" />
                <span class="left-value">10</span>
                <span class="center-value">50</span>
                <span class="right-value">100</span>
            </div>
            <div class="stat-box">
            <span class="stat-title">Likes</span>
                <i class="fas fa-info-circle information"></i>
                <div class="skills" data-percent="40%">
                    <div class="title-bar">
                        <h5>You</h5>
                    </div>
                    <span class="procent font-blue">40</span>
                    <div style="clear: both;"></div>
                    <div class="skillbar blue"></div>  
                </div>
                <div class="skills" data-percent="80%">
                        <div class="title-bar">
                            <h5>NOS</h5>
                        </div>
                        <span class="procent font-red">80</span>
                        <div style="clear: both;"></div>
                        <div class="skillbar red"></div>  
                </div>
                <hr class="x-as" />
                <span class="left-value">10</span>
                <span class="center-value">50</span>
                <span class="right-value">100</span>
            </div>
        </div>
        <div class="small-statistics">
            <div class="stat-box">
                <span class="stat-title">Can post</span>
                <i class="fas fa-info-circle information"></i>
                <div class="your-stat">
                    <span class="title-bar">You</span>
                    <i class="fas fa-check-circle check"></i>
                </div>
                <div class="competitor-stat">
                    <span class="title-bar">NOS</span>
                    <i class="fas fa-times-circle not-check"></i>
                </div>
            </div>
            <div class="stat-box">
            <span class="stat-title">Location</span>
                <i class="fas fa-info-circle information"></i>
                <div class="your-stat">
                    <span class="title-bar">You</span>
                    <i class="fas fa-check-circle check"></i>
                </div>
                <div class="competitor-stat">
                    <span class="title-bar">NOS</span>
                    <i class="fas fa-times-circle not-check"></i>
                </div>
            </div>
            <div class="stat-box">
            <span class="stat-title">Running ads</span>
                <i class="fas fa-info-circle information"></i>
                <div class="your-stat">
                    <span class="title-bar">You</span>
                    <i class="fas fa-check-circle check"></i>
                </div>
                <div class="competitor-stat">
                    <span class="title-bar">NOS</span>
                    <i class="fas fa-times-circle not-check"></i>
                </div>
            </div>
        </div>
        <div class="facebook-advice advice">
            <span class="advice-title">Facebook advice</span>
            <div class="skills" data-percent="80%">
                <span class="procent font-red">80%</span>
                <div style="clear: both;"></div>
                <div class="skillbar red"></div>  
            </div>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse. cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            <div class="buttons">
                <a href="#" class="button">Call Bram</a>
                <a href="#" class="button">Book a meeting</a>
            </div>
        </div>
    </div>
</section>
<section id="instagram-section">
    <div class="sidebar">
        <span class="title">Statistics</span>
        <ul>
            <li class="facebook-option" class="active"><i class="fab fa-facebook-square"></i><span class="nav-position">Facebook</span></li>
            <li class="instagram-option"><i class="fab fa-instagram"></i><span class="nav-position">Instagram</span></li>
            <li class="website-option"><i class="fas fa-globe"></i><span class="nav-position">Website</span></li>
            <li clas="conclusion-option"><i class="fas fa-check"></i><span class="nav-position">Conclusion</span></li>
        </ul>
        <a href="#" class="button" style="background: #dbecfd; font-weight: bold; color: #4da1ff; box-shadow: none;">Generate PDF</a>
    </div>
    <div class="facebook-right">
        <i class="fab fa-instagram section-icon"></i>
        <span class="section-title">Instagram statistics</span>     
        <span class="section-subtitle">Compare your Instagram page</span> 

        <div style="width: 100%; height: auto; padding-right: 70px;">
            <div class="chart-holder">
                <span class="stat-title">Likes on posts</span>
                <i class="fas fa-info-circle information"></i>
                <div class="averages">
                    <span class="your_averages">You<span class="data font-blue">250.1</span></span>
                    <span class="competitor_averages">NOS<span class="data font-red">320.42</span></span>
                </div>
                <div style="height: 220px">
                     <canvas id="canvas" style="display: block; height: 100%;" class="chartjs-render-monitor"></canvas>
                </div>
            </div>
        </div>

        <div class="statistics">
            <div class="stat-box">
                <span class="stat-title">Post each month</span>
                <i class="fas fa-info-circle information"></i>
                <div class="skills" data-percent="95%">
                    <div class="title-bar">
                        <h5>You</h5>
                    </div>
                    <span class="procent font-blue">95</span>
                    <div style="clear: both;"></div>
                    <div class="skillbar blue"></div>  
                </div>
                <div class="skills" data-percent="30%">
                        <div class="title-bar">
                            <h5>NOS</h5>
                        </div>
                        <span class="procent font-red">30</span>
                        <div style="clear: both;"></div>
                        <div class="skillbar red"></div>  
                </div>
                <hr class="x-as" />
                <span class="left-value">10</span>
                <span class="center-value">50</span>
                <span class="right-value">100</span>
            </div>
            <div class="stat-box">
            <span class="stat-title">Videos</span>
                <i class="fas fa-info-circle information"></i>
                <div class="skills" data-percent="80%">
                    <div class="title-bar">
                        <h5>You</h5>
                    </div>
                    <span class="procent font-blue">80</span>
                    <div style="clear: both;"></div>
                    <div class="skillbar blue"></div>  
                </div>
                <div class="skills" data-percent="20">
                        <div class="title-bar">
                            <h5>NOS</h5>
                        </div>
                        <span class="procent font-red">20</span>
                        <div style="clear: both;"></div>
                        <div class="skillbar red"></div>  
                </div>
                <hr class="x-as" />
                <span class="left-value">10</span>
                <span class="center-value">50</span>
                <span class="right-value">100</span>
            </div>
            <div class="stat-box">
            <span class="stat-title">Average post length</span>
                <i class="fas fa-info-circle information"></i>
                <div class="skills" data-percent="60%">
                    <div class="title-bar">
                        <h5>You</h5>
                    </div>
                    <span class="procent font-blue">60</span>
                    <div style="clear: both;"></div>
                    <div class="skillbar blue"></div>  
                </div>
                <div class="skills" data-percent="30%">
                        <div class="title-bar">
                            <h5>NOS</h5>
                        </div>
                        <span class="procent font-red">30</span>
                        <div style="clear: both;"></div>
                        <div class="skillbar red"></div>  
                </div>
                <hr class="x-as" />
                <span class="left-value">10</span>
                <span class="center-value">50</span>
                <span class="right-value">100</span>
            </div>
            <div class="stat-box">
            <span class="stat-title">Likes</span>
                <i class="fas fa-info-circle information"></i>
                <div class="skills" data-percent="40%">
                    <div class="title-bar">
                        <h5>You</h5>
                    </div>
                    <span class="procent font-blue">40</span>
                    <div style="clear: both;"></div>
                    <div class="skillbar blue"></div>  
                </div>
                <div class="skills" data-percent="80%">
                        <div class="title-bar">
                            <h5>NOS</h5>
                        </div>
                        <span class="procent font-red">80</span>
                        <div style="clear: both;"></div>
                        <div class="skillbar red"></div>  
                </div>
                <hr class="x-as" />
                <span class="left-value">10</span>
                <span class="center-value">50</span>
                <span class="right-value">100</span>
            </div>
        </div>
        <div class="instagram-advice advice">
            <span class="advice-title">Instagram advice</span>
            <div class="skills" data-percent="80%">
                <span class="procent font-red">80%</span>
                <div style="clear: both;"></div>
                <div class="skillbar red"></div>  
            </div>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse. cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            <div class="buttons">
                <a href="#" class="button">Call Bram</a>
                <a href="#" class="button">Book a meeting</a>
            </div>
        </div>
    </div>
</section>
</body>
<script>
$(document).ready(function(){
    
    startAnimation();

    $( ".instagram-option" ).click(function() {
        $(".facebook-option").removeClass("active");
        $(".website-option").removeClass("active");
        $(".conclusion-option").removeClass("active");
        $(".instagram-option").addClass("active");
        $("#facebook-section").css("display", "none");
        $("#instagram-section").css("display", "block");
        $("#website-section").css("display", "none");
        $("#conclusion-section").css("display", "none");
        startAnimation();
    });

    $( ".facebook-option" ).click(function() {
        $(".instagram-option").removeClass("active");
        $(".website-option").removeClass("active");
        $(".conclusion-option").removeClass("active");
        $(".facebook-option").addClass("active");
        $("#facebook-section").css("display", "block");
        $("#instagram-section").css("display", "none");
        $("#website-section").css("display", "none");
        $("#conclusion-section").css("display", "none");
        startAnimation();
    });

    function startAnimation(){
        jQuery('.skills').each(function(){

            jQuery(this).find('.skillbar').animate({
                width:jQuery(this).attr('data-percent')
            },1000); 
            
        });
    }                
});


var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
var config = {
    type: 'line',
    data: {
        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        datasets: [{
            pointHighlightFill: "#000",
             pointHighlightStroke: "rgba(75, 192, 192, 0.2)",
            borderWidth: 8,
            pointRadius: 0,
            label: 'My First dataset',
            backgroundColor: "#e36364",
            borderColor: "#e36364",
            data: [
                10,
                40,
                20,
                70,
                60,
                70,
                40
            ],
            fill: false,
        }, {
            borderWidth: 8,
            pointRadius: 0,
            label: 'My Second dataset',
            fill: false,
            backgroundColor: "#4da1ff",
            borderColor: "#4da1ff",
            data: [
                30,
                10,
                40,
                50,
                40,
                20,
                10
            ],
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        title: {
            display: false,
            
        },
        legend: {
            display: false
        },
        tooltips: {
            mode: 'index',
            intersect: false,
            bevelWidth: 3,
            bevelHighlightColor: 'rgba(255, 255, 255, 0.75)',
            bevelShadowColor: 'rgba(0, 0, 0, 0.5)'
        },
        hover: {
            mode: 'nearest',
            intersect: true
        },
        scales: {
            xAxes: [{
                ticks: {
                  fontColor: "#b7b7b7", // this here
                },
                display: true,
                gridLines: {
                    color: "rgba(0, 0, 0, 0)",
                },
                scaleLabel: {
                    display: true,
                    labelString: ''
                }
            }],
            yAxes: [{
                gridLines: { color: "#b7b7b7" }, 

                ticks: {
                  maxTicksLimit: 4,
                  fontColor: "#b7b7b7"
                },
                display: true,
                scaleLabel: {
                    display: true,
                    labelString: ''
                }
            }]
        }
    }
};


$.getScript("https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js", function () { window.onload = function() {
    var ctx = document.getElementById('canvas').getContext('2d');
    window.myLine = new Chart(ctx, config);
    }    
});



</script>
</html>