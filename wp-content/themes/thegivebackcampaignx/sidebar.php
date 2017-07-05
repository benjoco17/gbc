<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package thegivebackcampaign
 */

// if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	// return;
// }
?>



<aside>

<section>
  <!--<ul class="homeside">

    <li><a href="https://thegivebackcampaign.com/business/signup" ><img src="https://thegivebackcampaign.com/images/b1.png" border="0" width="280" height="120"></a></li>

    <li style="margin-top:5px;"><a href="https://thegivebackcampaign.com/organisation/signup" ><img border="0" src="https://thegivebackcampaign.com/images/b2.png" width="280" height="120"></a></li>

    <li style="margin-top:5px;"> <a href="https://thegivebackcampaign.com/consumer/signup"><img border="0" src="https://thegivebackcampaign.com/images/b3.png" width="280" height="120"></a></li>

  </ul>-->
</section>
<style>.content > aside section{margin:0px;}

.so_line a{color: #0089B5 !important;} .imagh img{width:100% !important;}</style>
        <section class="columns imagh" style="text-align:center;">
<a href="https://thegivebackcampaign.com/shop/3078/AutoEuropeCarRentals"><span class="img-border"><img src="https://thegivebackcampaign.com/images/products/autoeuropecarrentals2.jpg"></span></a>
<br><br><span style="text-align:center;"><strong>Give Back: 2.50%</strong></span>
</section>
<section>


  <h3><span>Find a Cause to Support</span></h3>

  <form id="fundraising_search_frm f" action="https://thegivebackcampaign.com/search/" method="post">

    <fieldset>

      <legend>

      
      </legend>
        <input type="text" style="width: 52%;float:left;margin-right:2px;" name="search_by_keyword" id="search_by_keyword" placeholder="Enter Search Words" value="">
   <button type="submit">GO</button>
     </fieldset>

  </form>

</section>
<style>input, textarea, select {
    background: #fff none repeat scroll 0 0;
    border: 1px solid #ccc;
    border-radius: 2px;
    padding: 8px 3%;
}</style>


<section class="columns">
<h3><span>Shop Search</span></h3>
<form id="shopping_search" action="https://thegivebackcampaign.com/search/shopping" method="post">
    <fieldset>
      
        <input type="text" name="shopping_search_keywords" style="width: 52%;float:left;margin-right:2px;" value="" placeholder="Enter Search Words" id="shopping_search_keywords" size="">
         <button style="" type="submit">GO</button>
       
      
    </fieldset>
  </form><br><br><form id="shopping_search" action="https://thegivebackcampaign.com/shop/redirect" method="post">

          <select style="width:59%;float:left;margin-right:2px;" name="catgoryul" required="">
            <option value="">Select a Category</option>
                         <option value="https://thegivebackcampaign.com/shop/category/49"> Accessories</option>

                        <option value="https://thegivebackcampaign.com/shop/category/50"> Art / Photo / Music</option>

                        <option value="https://thegivebackcampaign.com/shop/category/51"> Automotive</option>

                        <option value="https://thegivebackcampaign.com/shop/category/75"> Babies / Children</option>

                        <option value="https://thegivebackcampaign.com/shop/category/53"> Beauty</option>

                        <option value="https://thegivebackcampaign.com/shop/category/54"> Books/Media</option>

                        <option value="https://thegivebackcampaign.com/shop/category/55"> Business</option>

                        <option value="https://thegivebackcampaign.com/shop/category/56"> Careers</option>

                        <option value="https://thegivebackcampaign.com/shop/category/57"> Clothing/Apparel</option>

                        <option value="https://thegivebackcampaign.com/shop/category/73"> Competitions</option>

                        <option value="https://thegivebackcampaign.com/shop/category/58"> Computer &amp; Electronics</option>

                        <option value="https://thegivebackcampaign.com/shop/category/80"> Crafts</option>

                        <option value="https://thegivebackcampaign.com/shop/category/59"> Department Stores/Malls</option>

                        <option value="https://thegivebackcampaign.com/shop/category/88"> Easter</option>

                        <option value="https://thegivebackcampaign.com/shop/category/60"> Entertainment</option>

                        <option value="https://thegivebackcampaign.com/shop/category/61"> Family</option>

                        <option value="https://thegivebackcampaign.com/shop/category/62"> Financial Services</option>

                        <option value="https://thegivebackcampaign.com/shop/category/63"> Food &amp; Drinks</option>

                        <option value="https://thegivebackcampaign.com/shop/category/79"> Fundraising</option>

                        <option value="https://thegivebackcampaign.com/shop/category/64"> Games &amp; Toys</option>

                        <option value="https://thegivebackcampaign.com/shop/category/65"> Gifts &amp; Flowers</option>

                        <option value="https://thegivebackcampaign.com/shop/category/66"> Health and Wellness</option>

                        <option value="https://thegivebackcampaign.com/shop/category/67"> Home &amp; Garden</option>

                        <option value="https://thegivebackcampaign.com/shop/category/68"> Insurance</option>

                        <option value="https://thegivebackcampaign.com/shop/category/81"> Jewellery and Watches</option>

                        <option value="https://thegivebackcampaign.com/shop/category/76"> Learning and Education</option>

                        <option value="https://thegivebackcampaign.com/shop/category/77"> Office/Stationary</option>

                        <option value="https://thegivebackcampaign.com/shop/category/78"> Other</option>

                        <option value="https://thegivebackcampaign.com/shop/category/82"> Pets</option>

                        <option value="https://thegivebackcampaign.com/shop/category/86"> Property and Real Estate</option>

                        <option value="https://thegivebackcampaign.com/shop/category/69"> Recreation &amp; Leisure</option>

                        <option value="https://thegivebackcampaign.com/shop/category/70"> Sports &amp; Fitness</option>

                        <option value="https://thegivebackcampaign.com/shop/category/71"> Telecommunications</option>

                        <option value="https://thegivebackcampaign.com/shop/category/72"> Travel</option>

                        <option value="https://thegivebackcampaign.com/shop/category/85"> Valentines Day</option>

                      </select> <button style="" type="submit">GO</button>
       </form>
        </section>
        
<!--<section class="columns">
<h3><span>Local Businesses </span></h3>
<form id="shopping_search" action="https://thegivebackcampaign.com/home/localbusiness/Shoplocally" method="post">
    <fieldset>
      
        <input type="text" name="search1" style="width: 52%;float:left;margin-right:2px;"  value="" placeholder="Enter Search Words" id="shopping_search_keywords" size="" />
         <button style="" type="submit">GO</button>
       
      
    </fieldset>
  </form><br><br><form id="shopping_search" action="https://thegivebackcampaign.com/redirect/redirect" method="post">

          <select style="width:59%;float:left;margin-right:2px;" name="catgoryul" required>
           <option value="">Select a Category</option>             <option value="https://thegivebackcampaign.com/shop/businesscategory/157"> Accountants</option>

                        <option value="https://thegivebackcampaign.com/shop/businesscategory/66"> Adult Education</option>

                        <option value="https://thegivebackcampaign.com/shop/businesscategory/24"> Advertising</option>

                        <option value="https://thegivebackcampaign.com/shop/businesscategory/42"> Airlines</option>

                        <option value="https://thegivebackcampaign.com/shop/businesscategory/67"> Art Schools</option>

                        <option value="https://thegivebackcampaign.com/shop/businesscategory/53"> Beauty Salons</option>

                        <option value="https://thegivebackcampaign.com/shop/businesscategory/29"> Cafes</option>

                        <option value="https://thegivebackcampaign.com/shop/businesscategory/51"> Day Spas</option>

                        <option value="https://thegivebackcampaign.com/shop/businesscategory/63"> Driving Schools</option>

                        <option value="https://thegivebackcampaign.com/shop/businesscategory/166"> Mechanic</option>

                        <option value="https://thegivebackcampaign.com/shop/businesscategory/61"> Tyres</option>

                      </select> <button style="" type="submit">GO</button>
       </form>
        </section>-->      

</aside>