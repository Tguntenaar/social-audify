<?php
  $facebook_blocks = array(
    ["type" => "fb_likes",
      "name" => "Likes",
      "fb_name" => "country_page_likes",
      "desc" => "The likes you have on your page.",
      "is_icon" => 0
    ],
    ["type" => "fb_pem",
      "name" => "Posts each month",
      "fb_name" => "totalPostLastMonth",
      "desc" => "The number of posts per month.",
      "is_icon" => 0
    ],
    ["type" => "fb_apl",
      "name" => "Average post length",
      "fb_name" => "avgMessageLength",
      "desc" => "The average length of a post.",
      "is_icon" => 0
    ],
    ["type" => "fb_ntv",
     "name" => "Videos",
     "fb_name" => "native_videos",
     "desc" => "A Facebook Video is the most engaging type of content on Facebook. It will likely improve the overall engagement.",
     "is_icon" => 0
    ],
    ["type" => "fb_tab",
     "name" => "Talking about page",
     "fb_name" => "talking_about_count",
     "desc" => "Is a measure how many people are talking about your page and content in the last 7 days.",
     "is_icon" => 0
    ],
    ["type" => "fb_cp",
     "name" => "Can post",
     "fb_name" => "can_post",
     "desc" => "Allowing users to post to a page is a good first step in increasing  average engagement.",
     "is_icon" => 1
    ],
    ["type" => "fb_loc",
     "name" => "Location",
     "fb_name" => "location",
     "desc" => "If the location of the page is provided.",
     "is_icon" => 1
     ]
  );

  $facebook_ad_blocks = array(
    ["type" => "fb_ads",
      "name" => "Running ads",
      "fb_name" => "fb_ads",
      "desc" => "Click here to watch if this page is currently running ads.",
      "is_comp" => 0
    ],
    ["type" => "fb_ads_comp",
      "name" => "Competitor running ads",
      "fb_name" => "fb_ads_comp",
      "desc" => "Click here to watch if this page is currently running ads.",
      "is_comp" => 1
    ]
);

  $instagram_blocks = array(
    ["type" => "insta_nof",
      "name" => "Followers",
      "ig_name" => "followers_count",
      "desc" => "Number of followers."
    ],
    ["type" => "insta_ae",
      "name" => "Average engagement",
      "ig_name" => "avgEngagement",
      "desc" => "Average engagement."
    ],
    ["type" => "insta_nplm",
      "name" => "Number of posts last month",
      "ig_name" => "postsLM",
      "desc" => "Number of posts last month."
    ],
    ["type" => "insta_nopf",
      "name" => "Following",
      "ig_name" => "follows_count",
      "desc" => "Number of accounts followed."
    ],
    ["type" => "insta_ac",
      "name" => "Average Comments",
      "ig_name" => "averageComments",
      "desc" => "Number of accounts followed."
    ],
    ["type" => "insta_al",
      "name" => "Average Likes",
      "ig_name" => "averageLikes",
      "desc" => "Number of accounts followed."
    ]
  );

  $instagram_graph_blocks = array(
    ["type" => "insta_hashtag",
      "name" => "Top five hashtags",
      "ig_name" => "hashtags",
      "desc" => "Most used hashtags on specified instagram."
    ],
    ["type" => "insta_lpd",
      "name" => "Likes per day",
      "ig_name" => "likesPerPost",
      "desc" => "Likes per day."
    ],
  );

  $website_blocks = array(
    ["type" => "website_pixel",
     "name" => "Facebook Pixel",
     "db_name" => "facebook_pixel",
		 "comp_name" => "fp_c",
     "desc" => "Does the website implement Facebook Pixel.",
     "is_icon" => 1
    ],
    ["type" => "website_ga",
     "name" => "Google Analytics",
     "db_name" => "google_analytics",
		 "comp_name" => "ga_c",
     "desc" => "Does the website utilize Google Analytics.",
     "is_icon" => 1
    ],
    ["type" => "website_googletag",
     "name" => "Google Tagmanager",
     "db_name" => "google_tagmanager",
		 "comp_name" => "gt_c",
     "desc" => "Does the website implement Google Tagmanager.",
     "is_icon" => 1
    ],
    ["type" => "website_mf",
     "name" => "Mobile Friendly",
     "db_name" => "mobile_friendly",
		 "comp_name" => "mf_c",
     "desc" => "Passes the Google Mobile Friendly check.",
     "is_icon" => 0
    ],
    ["type" => "website_lt", // DEZE
     "name" => "Desktop Load Time",
     "db_name" => "load_time",
		 "comp_name" => "lt_c",
     "desc" => "The time to interactive of the desktop website in ms.",
     "is_icon" => 0
    ],
    ["type" => "website_ws", // EN DEZE, NAMEN MOETEN NOG ANDERS
     "name" => "Mobile Load Time",
     "db_name" => "website_size",
		 "comp_name" => "ws_c",
     "desc" => "The time to interactive of the mobile website in ms.",
     "is_icon" => 0
    ],
  );
?>
