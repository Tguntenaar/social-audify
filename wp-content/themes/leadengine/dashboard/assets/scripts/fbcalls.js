"use strict";

function testAPI() {
  console.log('Welcome! Fetching your information.... ');
  FB.api('/me', function (response) {
    console.log(`Successful login for: ${response.name}`);
  });
}

function testBatch() {
  console.log('Batch...');

  var businessName = 'thomasguntenaar';
  var pageName = '1083104585201499';
  var batch = [
    {
      "method":"GET",
      "name":"get-ig",
      "relative_url":"me/accounts?fields=instagram_business_account"
    },
    {
      "method":"GET",
      "relative_url":`?ids={result=get-ig:$.data..instagram_business_account.id}&fields=business_discovery.username(${businessName}){username, media_count, followers_count, follows_count, media{timestamp, like_count, comments_count, caption}}`,
    },
    {
      "method":"GET",
      "relative_url":`/${pageName}?fields=country_page_likes,fan_count,picture{height, width},posts{message,created_time},albums{id,name,cover_photo.fields(images)}`,
    }];

  FB.api('/', 'POST', {batch: batch, include_headers: false}, function (response) {
    if (!response || response.error) {
      console.log({response});
      response.forEach(function (element) {

        console.log(element);
      });
    }
  });
}

function makeIGpromise(iba, client, competitor = 0) {
  var firstPromise = new Promise(function (resolve, reject) {
    if (iba) {
      resolve(iba);
    } else {
      console.log({iba});
    } 
  });

  var secondPromise = new Promise(function (resolve, reject) {
    firstPromise.then(function (iba) {
      if (iba) {
        console.log(getInstaQuery(iba, client.instagram));
        FB.api(getInstaQuery(iba, client.instagram), function (response) {
          if (response && !response.error) {
            response.iba_id = iba;
            resolve(response);
          }
          var type = (competitor) ? 'competitor' : 'client';
          resolve({error:`The selected ${type} has an invalid instagram`});
        });

      } else {
        // error occured retrieving the instagram business account
        resolve({error:'invalid instagram', iba});

      }
    });
  });

  return secondPromise;
}

function makeFbPromise(client, competitor = 0) {
  var nestedPromise = new Promise(function (resolve, reject) {
    console.log(getFbQuery(client.facebook));

    FB.api(getFbQuery(client.facebook), function (response) {
      if (response && !response.error) {
        resolve(response);
      }
      var str = (competitor) ? 'competitor' : 'client';
      reject(`The selected ${str} has an invalid facebook`);
    });
  });

  return nestedPromise;
}

/**
 * ! client data is een object geen
 * ! facebook en instagram options rekening mee houden.
 */
function makeApiCalls(instance) {
  console.log({instance});
  const {client, competitor, page, options, currency, instagram_business_accounts} = instance;

  var emptyPromise = Promise.resolve('This option is disabled');

  var igPromise = emptyPromise;
  var igPromiseComp = emptyPromise;
  var fbPromise = emptyPromise;
  var fbPromiseComp = emptyPromise;

  var facebook_data, instagram_data, coverPhotoSize, fbPageData;

  var promisesArray = [];


  if (options.instagram_checkbox) {
    console.log('instagram_business_accounts.current');
    console.log(instagram_business_accounts);
    
    igPromise = makeIGpromise(instagram_business_accounts.current, client);
    if (competitor && page.type === 'audit') { igPromiseComp = makeIGpromise(instagram_business_accounts.current, competitor, 1); }
  }

  if (options.facebook_checkbox) {
    fbPromise = makeFbPromise(client);
    if (competitor && page.type === 'audit') { fbPromiseComp = makeFbPromise(competitor, 1); }
  }

  // push them on the array
  promisesArray = [...promisesArray, igPromise, fbPromise];


  if (competitor && page.type === 'audit') {
    promisesArray = [...promisesArray, igPromiseComp, fbPromiseComp];
  }

  Promise.all(promisesArray).then(function (allResponses) {

    console.log(allResponses);

    instagram_data = handleResponseInsta(allResponses[0]);
    coverPhotoSize = handleResponseCoverphoto(allResponses[1]);
    fbPageData = handleResponsePageInfo(allResponses[1]);
    facebook_data = {...{coverPhotoSize}, ...fbPageData};

    client.data = {instagram_data, facebook_data};

    if (competitor && page.type === 'audit') {
      instagram_data = handleResponseInsta(allResponses[2]);
      coverPhotoSize = handleResponseCoverphoto(allResponses[3]);
      fbPageData = handleResponsePageInfo(allResponses[3]);
      facebook_data = {...{coverPhotoSize}, ...fbPageData};
      competitor.data = {instagram_data, facebook_data};
    }

    if ((allResponses[0] != undefined && !!allResponses[0].error) || (competitor && (allResponses[2] != undefined && !!allResponses[2].error))) {
      
      // FIXME: dit is om te kijken of een manual bit aan moet voor client en competitor apart
      // var i = 0;
      // if ((allResponses[0] != undefined && !!allResponses[0].error)) {
      //   i++;
      //   if ((competitor && (allResponses[2] != undefined && !!allResponses[2].error))) {
      //     i++;
      //   }
      // }

      // TODO:
      if ((allResponses[0] != undefined && !!allResponses[0].error) && (competitor && (allResponses[2] != undefined && !!allResponses[2].error))) {
        var i = 2;
      } else if (!!allResponses[0].error) {
        var i = 1;
      } else if ((competitor && !!allResponses[2].error)) {
        var i = 0;
      }

      askToContinue(client, page, options, competitor, i);
    } else {
      post_ajax(client, page, options, competitor, currency);
    }

  }).catch((reason) => {
    showBounceBall(false);
    console.log(`%c Reason is ${reason}`, 'color: red');
    console.log({reason});

    var msg = (!!reason.error) ? reason.error.message : reason;

    showModal(initiateModal('errorModal', 'error', {
      'text': `${msg}`,
      'subtext': `Choose another candidate.`,
    }));

    if (Instance.page.type == 'audit') {
      nextPrev( (reason.includes('competitor')) ? -1 : -2);
    } else if (Instance.page.type == 'report') {
      nextPrev(-4);
    }
  });
}


function askToContinue(client, page, options, competitor, manualType) {
  var str = (manualType == 0) ? 'both the client and competitor' : (manualType == 1) ? 'the client' : 'the competitor';

  showModal(initiateModal('instagramErrorModal', 'confirm', {
    text: `Couldn\'t gather instagram data for ${str}`,
    subtext: 'The most common reasons are: the username you specified is not a business instagram or it is an invalid username. Would you like to continue to the audit and fill in the blanks manually?',
    confirm: 'continue-to-audit',
    cancel: 'cancel-making-audit',
  }));

  $('#continue-to-audit').click(function() {
    page.manual = (manualType == 1 || manualType == 2) ? 1 : 0;
    page.competitor_manual = (manualType == 0 || manualType == 2) ? 1 : 0;

    post_ajax(client, page, options, competitor);
  });

  $('#cancel-making-audit').click(function() {
    nextPrev(-1);
    showBounceBall(false);
  });
}


function post_ajax(client, page, options, competitor = false, currency = null) {
  if (typeof competitor !== 'object') {
    competitor = 'false';
  }

  var data = {
    'client': JSON.stringify(client),
    'page_info' : JSON.stringify(page),
    'options' : JSON.stringify(options),
    'competitor' : JSON.stringify(competitor),
    'currency' : JSON.stringify(currency)
  };

  console.log({data});

  if (page.type === 'audit') {
    data.action = 'update_meta_audit';
  } else if (page.type === 'report') {
    data.action = 'update_meta_report';
  }

  $.ajax({
    type: "POST",
    url: ajaxurl,
    data: data,
    success: function (response) {
      console.log({response});
      window.location.replace(`${response.slug}`);
    },
    error: function (response) {
     console.log({response});
     showBounceBall(false);
    }
  });
}

function getIGBusinessID(response) {
  if (!response || response.error) {
    // alert
    showModal(initiateModal('errorModal', 'error', {
      'text': "Problem with Instagram",
      'subtext': "Error occured retrieving your instagram account id."
    }));
    reject('Error occured retrieving your instagram account id.');
} else {
    for (var i = 0; i < response.data.length; i++) {
      if (response.data[i].instagram_business_account) {
        return response.data[i].instagram_business_account.id;
      }
    }
  }
}

function handleResponseInsta(response) {
  if (!response || response.error) {
    // manual
    return {
      avgEngagement: 0,
      postsLM: 0,
      likesPerPost: 0,
      averageComments: 0,
      averageLikes: 0,
      followers_count: 0,
      follows_count: 0
    };
  } else if (response === 'This option is disabled') {
    return {};
  } else {
    var bd = response.business_discovery;
    var info = unpackMediaInfo(bd.media);

    info.followers_count = bd.followers_count;
    info.follows_count = bd.follows_count;
    return info;
  }
}

function unpackMediaInfo(media) {
  var tLikes, tComments, tPosts, likesLM, commentsLM, postsLM, averageComments,
      averageLikes, avgEngagement;
  tLikes = tComments = tPosts = likesLM = commentsLM = postsLM = 0;
  var captions = [];
  var likesPerPost = [];

  var data = media.data;

  data.forEach(function (post) {
    tPosts += 1;
    tLikes += post.like_count;
    tComments += post.comments_count;
    likesPerPost.push(post.like_count);
    captions.push(post.caption);

    if (dayDifference(post.timestamp) < 31) {
      postsLM += 1;
      likesLM += post.like_count;
      commentsLM += post.comments_count;
    }
  });
                                                      // 25
  avgEngagement = averageEngagement(tLikes, tComments, tPosts);
  avgEngagementLM = averageEngagement(likesLM, commentsLM, postsLM);
  averageComments = (tComments / Math.max(1, tPosts)).toFixed(2);
  averageLikes = (tLikes / Math.max(1, tPosts)).toFixed(2);

  // Deze variabelen zouden nog meegegeven kunnen worden 'tLikes', 'tComments',
  // 'tPosts', 'avgEngagementLM', 'likesLM', 'commentsLM',
  var returnMedia = {
    avgEngagement,
    postsLM,
    likesPerPost,
    averageComments,
    averageLikes
  };

  returnMedia.hashtags = getHashtags(captions);

  return returnMedia;
}

function dayDifference(time) {
  var date = new Date((time || "").replace(/-/g,"/").replace(/[TZ]/g," ")),
    diff = (((new Date()).getTime() - date.getTime()) / 1000),
    day_diff = Math.floor(diff / 86400);
  return day_diff;
}

// Return the smallest of two numbers.
function smallest(int1, int2) {
  if (int1 < int2) { return int1; }
  return int2;
}

// TODO: tripple check this function
function getHashtags(captions) {
  var re = /#([a-zA-Z0-9]+)/gm;
  var counts = {};
  var hashtag;

  // Read all hashtags out of string.
  while ((hashtag = re.exec(captions)) != null) {
    var num = hashtag[1];
    counts[num] = counts[num] ? counts[num] + 1 : 1;
  }

  // dictioniary into 2D list
  var sortable = [];
  for (var key in counts) {
    sortable.push([key, counts[key]]);
  }

  // sort list
  sortable.sort(function (a, b) {
    return b[1] - a[1];
  });

  if (sortable.length != 0) {
    // transpose
    var ht = sortable[0].map((col, i) => sortable.map(row => row[i]));

    // get top 5
    ht[0] = ht[0].slice(0, 4);
    ht[1] = ht[1].slice(0, 4);

    return ht;
  } else {
    return [[], []];
  }
}

function getCoverPhotosDetails(response) {
  for (var i = 0; i < response.data.length; i++) {
    var album = response.data[i];
    if (album.name === "Cover Photos") {
      var w = album.cover_photo.images[0].width,
          h = album.cover_photo.images[0].height;
      return h + " X " + w;
    }
  }
  return '0 X 0';
}

function unpackPageInfo(response) {
  var loc, vid, pst;

  loc = (!response.location) ? 0 : 1;
  vid = (!response.videos) ? 0 : response.videos.data.length;
  pst = (!response.posts) ? {} : response.posts.data;

  var country_page_likes = response.fan_count,
      pfData = response.picture.data,
      pf_picture_size = pfData.height + " X " + pfData.width,
      posts = pst,
      can_post = response.can_post,
      talking_about_count = response.talking_about_count,
      native_videos = vid,
      location = loc;

  var nLink, nStatus, nPhoto, nVideo, nOffer, totalMessageLength, tPosts, avgMessageLength, totalPostLastMonth, runningAdds;
  nLink = nStatus = nPhoto = nVideo = nOffer = totalMessageLength = tPosts = avgMessageLength = totalPostLastMonth = runningAdds = 0;

  for (var i = 0; i < posts.length; i++) {
    if (posts[i].created_time) {
      if (dayDifference(posts[i].created_time) < 31) {
        totalPostLastMonth += 1;
      }
    }
    if (posts[i].message) {
      totalMessageLength += posts[i].message.length;
    }
    tPosts += 1;
    if (posts[i].type == "photo") {
      nPhoto += 1;
    } else if (posts[i].type == "status") {
      nStatus += 1;
    } else if (posts[i].type == "link") {
      nLink += 1;
    } else if (posts[i].type == "video") {
      nVideo += 1;
    } else if (posts[i].type == "offer") {
      nOffer += 1;
    }
  }

  avgMessageLength = ( totalMessageLength / Math.max(1, tPosts) ).toFixed(2);

  return {
      totalPostLastMonth, country_page_likes, 
      pf_picture_size, location,
      nLink, nStatus, nPhoto, nVideo, nOffer, 
      avgMessageLength, runningAdds, can_post,
      talking_about_count, native_videos, };
}

function getIGBusinessAccountsQuery() {
  return '/me/accounts?fields=instagram_business_account{name},name';
}

function getAdAccountsQuery() {
  return 'me/adaccounts?fields=name';
}

function getFbQuery(page_name) {
  return `/${page_name}?fields=country_page_likes,fan_count,picture{height, width},posts{message,created_time},albums{id,name,cover_photo.fields(images)}, location, videos, can_post, talking_about_count`;
}

function getInstaQuery(iba_id, business_name) {
  return `${iba_id}?fields=business_discovery.username(${business_name}){username, media_count, followers_count, follows_count, media{timestamp, like_count, comments_count, caption}}`;
}

function getCampaignsQuery(act_id, edge) {
    return `/${act_id}?fields=currency,${edge}{id,name,insights{reach, impressions, cpc, cpm, cpp, ctr, frequency, spend, unique_inline_link_clicks, website_purchase_roas}}`;
}

function handleResponseCoverphoto(response) {
  if (!response || response.error) {
    // alert
    showModal(initiateModal('errorModal', 'error', {
      'text': "Problem with Facebook",
      'subtext': "Error occured retrieving the cover_photo album."
    }));
  } else if (response === 'This option is disabled') {
    return {};
  } else {
    return getCoverPhotosDetails(response.albums);
  }
}

function handleResponsePageInfo(response) {
  if (!response || response.error) {
    // alert
    showModal(initiateModal('errorModal', 'error', {
      'text': "Problem with Facebook",
      'subtext': "Error occured retrieving facebook info from last month."
    }));
  } else if (response === 'This option is disabled') {
    return {};
  } else {
    return unpackPageInfo(response);
  }
}

function averageEngagement(likes, comments, numPosts) {
  return (likes + comments) / numPosts;
}
