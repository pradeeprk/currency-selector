<?php
/**
* Plugin Name: Currency Selector 
* Description: Currency Selector 
* Version: 1
* Author: Pradeep
*/
if (!defined('ABSPATH'))
die();

class CurrencySelector {


	public function __construct() {
		
	$this->setup_hooks();
	
	}
	
	public function setup_hooks() {
			
		add_action('add_meta_boxes', array( $this,'currency_meta_boxes'));
		add_action('save_post', array( $this,'save_cr_meta'));		
		add_filter( 'woocommerce_rest_prepare_product_object', array( $this,'wc_add_currencies'), 10, 3 );
	}
	
	function currency_meta_boxes(){	
		add_meta_box('currency_selector', 'Currencies', array( $this,'currency_selector'), 'product','normal', 'low',$meta);
	}
	
	function currency_selector($post){
				
		$cr_currencies = unserialize(get_post_meta($post->ID,'cr_currencies',true));		
		$currencies = get_woocommerce_currencies();
				
		?>
		<table >
		<thead>
		<tr>
		<td> Currency</td>
		<td> Value </td>
		</tr>	
		</thead>
		<?php for($i=0;$i<=2;$i++){ ?>
		<tr>
		<td><?php $this->set_currency($currencies,$i,$cr_currencies[$i]['type']) ; ?></td>
		<td><input type="text" name="cr_currencies[<?php echo $i; ?>][value]" value="<?php echo $cr_currencies[$i]['value'] ;?>"/></td>
		</tr>
		<?php } ?>
		</table>		
		<?php
		
	}
	
	function set_currency($currencies,$i,$current_value){ 
		
		echo "<select name='cr_currencies[".$i."][type]'>";
		echo '<option value=""> Please select a Currency</option>';
		foreach($currencies as $symbol => $country){
		$selected= $symbol==$current_value?'selected="selected"':'';		
		?>
		<option value="<?php echo $symbol; ?>" <?php echo $selected; ?> ><?php echo $symbol.' '.$country;  ?> </option>	
		<?php	
		}
		echo '</select>';	
	
	}
	
	function save_cr_meta($post_id){
		
		$value = sanitize_text_field(serialize($_POST['cr_currencies']));
		
		if (isset($_POST['post_title'])  ) {			
			update_post_meta($post_id, 'cr_currencies', $value);
		
		}
	}
	
	function wc_add_currencies($response, $post, $request){
		
	$currencies = unserialize(get_post_meta($post->get_id(),'cr_currencies',true));
	$response->data['currencies'] = $currencies;
	return $response;
	
	}

}
$CurrencySelector = new CurrencySelector();