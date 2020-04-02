<?php

class signature
{
  private $first_name;
  private $last_name;
  private $jobtitle;
  private $company;
  private $website;
  private $mobile_phone_number;
  private $facebook;
  private $instagram;
  private $linkedin;
  private $email;
  private $img_url;
  private $color;

  /**
   * user and wp_user_meta_data
   */
  public function __construct($user, $meta) 
  {
    $this->first_name = (isset($meta['first_name'])) ? $meta['first_name'][0] : "";
    $this->last_name = (isset($meta['last_name'])) ? $meta['last_name'][0] : "";
    $this->company = (isset($meta['rcp_company'])) ? $meta['rcp_company'][0] : "";
    
    $this->website = (isset($meta['rcp_website'])) ? $meta['rcp_website'][0] : "";
    $this->jobtitle = (isset($meta['rcp_jobtitle'])) ? $meta['rcp_jobtitle'][0] : ""; 
    $this->mobile_phone_number = (isset($meta['rcp_number'])) ? $meta['rcp_number'][0] : "";  
    $this->facebook = (isset($meta['rcp_facebook'])) ? $meta['rcp_facebook'][0] : "";
    $this->instagram = (isset($meta['rcp_instagram'])) ? $meta['rcp_instagram'][0] : ""; 
    $this->linkedin = (isset($meta['rcp_linkedin'])) ? $meta['rcp_linkedin'][0] : "";

    $upload_id = $user->signature;
    $this->img_url = wp_get_attachment_url($upload_id);
    $this->email = $user->email;
    $this->color = $user->color_audit;
  }

  public function __get($name) 
  {
    return $this->$name;
  }

  public function __toString()
  {
    try 
    {
      return $this->html_string();
    } 
    catch (Exception $exception) 
    {
      return '';
    }
  }

  public function html_string() {
    $website_label = str_replace(['https://', 'http://'], '', $this->website);
    $image_html = $this->img_url ?
      "<td style='padding-top: 20px; padding-bottom: 20px; padding-right: 20px; font-size: 10pt; font-family: Arial; width: 125px' vAlign='middle'>
        <img src='{$this->img_url}' alt='upload company logo' width='250' id='signature-img'></td>" : '';
    $facebook_string = ($this->facebook != "") ? "<a href='{$this->facebook}'><i style='color:{$this->color}' class='fab fa-facebook-square'></i></a>&nbsp;" : "";
    $instagram_string = ($this->instagram != "") ? "<a href='{$this->instagram}'><i style='color:{$this->color}' class='fab fa-instagram'></i></a>&nbsp;" : "";
    $website_string = ($this->website != "") ? "<a href='{$this->website}'><i style='color:{$this->color}' class='fas fa-globe'></i></a>&nbsp;" : "";
    $linkedin_string = ($this->linkedin != "") ? "<a href='{$this->linkedin}'><i style='color:{$this->color}' class='fab fa-linkedin'></i></a>" : "";
    
    $website_string_2 = ($this->website != "") ? "<span style='color:{$this->color}'><strong>w:</strong></span>
    <a style='text-decoration:none; color:#000000' href='{$this->website}'>{$website_label}</a><br>" : "";

    $mobile_string = ($this->mobile_phone_number != "") ? "<span style='color: {$this->color}'><strong>m:</strong></span> {$this->mobile_phone_number}</span><br><br>" : "";
    return "
      <link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.3.1/css/all.css' integrity='sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU' crossorigin='anonymous'>
      <table style='border-collapse: collapse; border-spacing: 0; width: 525px' cellSpacing='0' cellPadding='0'>
        <tbody>
          <tr cellspacing='0' cellpadding='0' style='padding:0!important;'>{$image_html}
            <td cellspacing='0' cellpadding='0' style='vertical-align: middle !important; 
                display: table-cell; font-size: 10pt; font-family: Arial; width: 400px; padding-left: 20px; border-left: {$this->color} 1px solid' valign='top'>
              <strong style='font-size: 11pt'><span style='color: {$this->color}'>{$this->first_name} {$this->last_name}</strong> | {$this->jobtitle}<br><br>
              <span style='color:{$this->color}'>{$this->company}</span><br>
              <span style='color:{$this->color}'><strong>e: </strong></span>{$this->email}<br>
              {$website_string_2}
              {$mobile_string}
              {$facebook_string}
              {$instagram_string}
              {$website_string}
              {$linkedin_string}
            </td>
          </tr>
        </tbody>
      </table>";
  }

  public function plain_text()
  {
    return "--\n".$this->firstname." ".$this->lastname." | ".$this->jobtitle.
    "\na:".$this->company."\ne:".$this->email." | w:".$this->website."\nm:".
    $this->mobile_phone_number;
  }
}
?>