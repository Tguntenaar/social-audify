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

  /**
   * TODO: echo $signature
   */
  public function __toString()
  {
      try 
      {
          return (string) $this->html();
      } 
      catch (Exception $exception) 
      {
          return '';
      }
  }

  public function html()
  {
  ?>
    <table style=" border-collapse: collapse; border-spacing: 0; width: 525px" cellSpacing="0" cellPadding="0">
      <tbody>
        <tr cellspacing="0" cellpadding="0" style="padding:0!important;">
          <?php if ($this->img_url): ?>
          <td style="padding-top: 20px; padding-bottom: 20px; padding-right: 20px; font-size: 10pt; font-family: Arial; width: 125px" vAlign="middle">
            <img src="<?php echo $this->img_url ?>" alt="upload company logo" width="250" id="signature-img">
          </td>
          <?php endif; ?>
          <td
            cellspacing="0" cellpadding="0" style="padding-top: 20px; padding-bottom: 20px;  vertical-align: middle !important;  display: table-cell; font-size: 10pt; font-family: Arial; width: 400px; padding-left: 20px; border-left: <?php echo $this->color ?> 1px solid"
            valign="top">
            <strong style="font-size: 11pt">
              <span style="font-size: 11pt; color: <?php echo $this->color ?>"><?php echo $this->first_name." ".$this->last_name ?> </strong> |
              <?php echo $this->jobtitle; ?></s><br><br><span style="color: #000000">
              <span style="color: <?php echo $this->color ?>"><span style="color: <?php echo $this->color ?>"></span></span>

              <span style="color: <?php echo $this->color ?>"><?php echo $this->company ?></span></span><br> 
              <a style="color: #000000"><span style="color: <?php echo $this->color ?>"><strong>e:  </strong></span><?php echo $this->email?></a> <br />
              <span style="color: <?php echo $this->color ?>"><strong>w:</strong></span> <a style="text-decoration: none; color: #000000"
                href="<?php echo $this->website; ?>"><?php echo str_replace(['https://', 'http://'], '', $this->website); ?></a> <br>
              <span style="color: <?php echo $this->color ?>"><strong>m:</strong></span> <?php echo $this->mobile_phone_number ?></span><br><br>
            <a href="<?php echo $this->facebook ?>"><i style="color: <?php echo $this->color ?>" class="fab fa-facebook-square"></i></a>&nbsp;
            <a href="<?php echo $this->instagram ?>"><i style="color: <?php echo $this->color ?>" class="fab fa-instagram"></i></a>&nbsp;
            <a href="<?php echo $this->website ?>"><i style="color: <?php echo $this->color ?>" class="fas fa-globe"></i></a>&nbsp;
            <a href="<?php echo $this->linkedin ?>"><i style="color: <?php echo $this->color ?>" class="fab fa-linkedin"></i></a></TD>
            <!-- TODO: 
            html to voegen aan mail controller
            signature color
            als een veld leeg is moet ie verdwijnen in de signature
          -->
        </tr>
      </tbody>
    </table>
  <?php
  }

  public function plain_text()
  {
    return "--\n".$this->firstname." ".$this->lastname." | ".$this->jobtitle.
    "\na:".$this->company."\ne:".$this->email." | w:".$this->website."\nm:".
    $this->mobile_phone_number;
  }
}
?>