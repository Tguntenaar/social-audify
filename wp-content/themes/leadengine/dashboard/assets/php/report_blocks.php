<?php
  $social_blocks = array(
    ["type" => "soc_pl",
      "name" => "FB page likes",
      "data" => "facebook_data",
      "fb_name" => "country_page_likes",
      "desc" => "The amount of likes the facebook page has",
      "fb" => 1
    ],
    ["type" => "soc_aml",
      "name" => "FB average message length",
      "data" => "facebook_data",
      "fb_name" => "avgMessageLength",
      "desc" => "The average length of a message on the facebook page",
      "fb" => 1
    ],
    ["type" => "soc_inf",
      "name" => "Insta number followers",
      "data" => "instagram_data",
      "fb_name" => "followers_count",
      "desc" => "The number of followers the instagram page has",
      "fb" => 0
    ],
    ["type" => "soc_inaf",
      "name" => "Insta number accounts followed",
      "data" => "instagram_data",
      "fb_name" => "follows_count",
      "desc" => "The number of accounts the instagram page follows",
      "fb" => 0
    ],
    ["type" => "soc_iae",
      "name" => "Insta average engagement",
      "data" => "instagram_data",
      "fb_name" => "avgEngagement",
      "desc" => "The average of activity on the instagram page",
      "fb" => 0
    ],
    ["type" => "soc_plm",
      "name" => "Insta number of post last month",
      "data" => "instagram_data",
      "fb_name" => "postsLM",
      "desc" => "The number of posts the instagram page added last month",
      "fb" => 0
    ],
  );

  $campaign_blocks = array(
    ["type" => "cam_rch",
      "name" => "Reach",
      "fb_name" => "reach",
      "desc" => "The average of the reach"
    ],
    ["type" => "cam_imp",
      "name" => "Impressions",
      "fb_name" => "impressions",
      "desc" => "The average of the impressions"
    ],
    ["type" => "cam_cpc",
      "name" => "Cost per click",
      "fb_name" => "cpc",
      "desc" => "The average cost per click"
    ],
    ["type" => "cam_cpm",
      "name" => "Cost per mile",
      "fb_name" => "cpm",
      "desc" => "The average cost per mile"
    ],
    ["type" => "cam_cpp",
      "name" => "Cost per pixel",
      "fb_name" => "cpp",
      "desc" => "The average cost per pixel"
    ],
    ["type" => "cam_ctr",
      "name" => "Click through ratio",
      "fb_name" => "ctr",
      "desc" => "The average ratio of the click throughs"
    ],
    ["type" => "cam_frq",
      "name" => "Frequency",
      "fb_name" => "frequency",
      "desc" => "The average frequency of the ads"
    ],
    ["type" => "cam_spd",
      "name" => "Spend",
      "fb_name" => "spend",
      "desc" => "The average amount spend"
    ],
    ["type" => "cam_lcl",
      "name" => "Link clicks",
      "fb_name" => "unique_inline_link_clicks",
      "desc" => "The average link clicks"
    ],
    ["type" => "cam_ras",
      "name" => "Return on ad spent",
      "fb_name" => "website_purchase_roas",
      "desc" => "The average return on ads spend"
    ]
  );
?>
