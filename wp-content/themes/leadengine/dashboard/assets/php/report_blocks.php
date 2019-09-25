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
    ["type" => "cam_imp",
      "name" => "Impressions",
      "fb_name" => "impressions",
      "desc" => "Average impressions",
      "currency" => 0
    ],
    ["type" => "cam_rch",
      "name" => "Reach",
      "fb_name" => "reach",
      "desc" => "Average reach",
      "currency" => 0
    ],
    ["type" => "cam_cpc",
      "name" => "Cost per click",
      "fb_name" => "cpc",
      "desc" => "Average cost per click",
      "currency" => 1
    ],
    ["type" => "cam_cpm",
      "name" => "Cost per mille",
      "fb_name" => "cpm",
      "desc" => "The average cost for 1,000 impressions",
      "currency" => 1
    ],
    ["type" => "cam_cpp",
      "name" => "Cost per pixel",
      "fb_name" => "cpp",
      "desc" => "The average cost to reach 1,000 people. This metric is estimated.",
      "currency" => 1
    ],
    ["type" => "cam_ctr",
      "name" => "Click through ratio",
      "fb_name" => "ctr",
      "desc" => "Average ratio click throughs",
      "currency" => 0
    ],
    ["type" => "cam_frq",
      "name" => "Frequency",
      "fb_name" => "frequency",
      "desc" => "Average frequency ads",
      "currency" => 0
    ],
    ["type" => "cam_spd",
      "name" => "Spend",
      "fb_name" => "spend",
      "desc" => "Average amount spend",
      "currency" => 1
    ],
    ["type" => "cam_lcl",
      "name" => "Link clicks",
      "fb_name" => "unique_inline_link_clicks",
      "desc" => "Average link clicks",
      "currency" => 0
    ],
    ["type" => "cam_ras",
      "name" => "Return on ad spent",
      "fb_name" => "website_purchase_roas",
      "desc" => "Average return on ads spend",
      "currency" => 0
    ]
  );
?>
