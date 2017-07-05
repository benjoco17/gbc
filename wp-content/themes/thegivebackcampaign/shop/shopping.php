<?php 
/**
Template Name: Shopping Page
**/
get_header(); ?>
<section class="content">

	<section class="main single">
       <?php $row=1;?>
       <section class="columns">
		 <form id="shopping_search" action="<?php bloginfo('url');?>/shop-online/shop-online-result" method="post">
			<fieldset>      
				<input type="text" name="shopping_search_keywords" style="float:left; margin-right:5px;" value="<?php echo $_POST['shopping_search_keywords']?>"id="shopping_search_keywords" size="28%" />   
				<button style="height: 38px;padding: 5px 17px 16px 16px;" name="submit" type="submit">Search</button>      
			</fieldset>
		  </form>   
       

	</section>
</section>

</section>

<?php


//SEARCH
// function search(){
if (isset($_POST['submit'])) {
	$searchKey = $_POST['shopping_search_keywords'];
	$country="";       
	$where="";

	if(isset($_SESSION['current_country'])){          
	    $current_country=$_SESSION['current_country'];            
	    $country="and im.inventory_id in (select inventory_id from inventory_country_master where country_id=".$current_country.")";
	}       
	if($searchKey){   
	   $searchKey=str_replace("'","\'",strtoupper($searchKey));
	   $where="and UPPER(CONCAT(inventory_title,inventory_desc,advertiser_title,advertiser_desc,advertiser_metadesc,advertiser_metatags)) LIKE '%".$searchKey."%' ".$country;          
	}

	$query = "SELECT * from inventory_manager as im join advertiser_manager as am on am.advertiser_id=im.advertiser_id where inventory_status=1 and inventory_startdate<= CURDATE() and inventory_enddate > CURDATE() $where order by im.inventory_id desc";
	$res = $wpdb->get_results($query);

	// echo '<pre>'; print_r($res); '</pre>';
	foreach ($res as $te => $s) {
		echo $s->inventory_title . '<br/>';
	}
// }

	//GET CURRENCY
	function getCurrency($currency_id){
		$where='';
		if($currency_id){
			$where="and currency_id= ".$currency_id;
		}
	   
		$query = "Select * from currency where status=1 ".$where;
		
		if($currency_id){
		   return $res = $wpdb->get_row($query);
		}else{
		   return $res = $wpdb->get_results($query);
		}
	}
}


?>

<?php get_footer(); ?>