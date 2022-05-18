<?php
/*
Plugin Name:SN Météo
Plugin URI:
Description: Widget pour afficher la météo
Version: 1.0
Author: idrissa Ndiouck & cheikh Séne
Author URI: http://github.com/weexduunx
License: GPLv2
*/

function widget_style()
{

    /*   CSS pour le plugin */

    wp_register_style('css',plugin_dir_url(__FILE__).'css/mdb.min.css');
    wp_enqueue_style('css');
    wp_register_style('style',plugin_dir_url(__FILE__).'style/style.css');
    wp_enqueue_style('style');

    /*inclus le fichier JS et jQuery*/

    wp_enqueue_script( 'script', plugins_url('js/mdb.min.js', __FILE__), array('jquery'));


}


function donnee_meteo($city)
{
    $apiKey = "71841940c09bcaa94a99e0cb37205cda";

    //$cityId = "Dakar";
    $openWeatherApiUrl = "https://api.openweathermap.org/data/2.5/weather?q=" . $city . "&appid=" . $apiKey;


    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $openWeatherApiUrl);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $reponse = curl_exec($ch);

    curl_close($ch);
    $donnee_meteo = json_decode($reponse);
    return $donnee_meteo;
}


function ip_details($IPaddress)
{
    $json       = file_get_contents("http://ipinfo.io/{$IPaddress}");
    $details    = json_decode($json);
    return $details;
}

class Sunu_Meteo_Widget extends WP_Widget
{
    // Constructeur principal
    public function __construct()
    {
        parent::__construct(
            'P_weather_widget',
            __('Sunu Météo Widget', 'text_domain'),
            array(
                'customize_selective_refresh' => true,
            )
        );
    }


	// le formulaire de widget (pour le backend)    

	public function form($instance)
    {

        $details    =   ip_details("41.82.171.182");

        $ville = $details->city;

	// analyser les paramètres actuels avec des valeurs par défaut
	//Cette fonction est utilisée dans WordPress pour permettre à la file ou au tableau de fusion dans un autre tableau.
<<<<<<< HEAD

=======
	
>>>>>>> 6ba28012bf4cb66e141a295c0b60f79d1d3363f5
        extract(wp_parse_args((array) $instance, $details)); ?>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('city')); ?>">
				<?php _e('Ville:', 'text_domain'); ?>
			</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('city')); ?>" 
			name="<?php echo esc_attr($this->get_field_name('city')); ?>" type="text" 
			value="<?php echo $ville; ?>" />
        </p>

    <?php }
    // Mettre à jour les paramètres du widget
    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['city']    = isset($new_instance['city']) ? wp_strip_all_tags($new_instance['city']) : '';
        return $instance;
    }
    // Afficher le widget
    public function widget($args, $instance)
    {
        extract($args);

        //Vérifiez les options du widget
        $ville     = isset($instance['city']) ? $instance['city'] : '';
        // WordPress Core before_Widget Hook (toujours appelé)
        echo $before_widget;

        $donnee = donnee_meteo($ville);
        $date = date("H:i:s");
    ?>


    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-md-12 col-lg-12 col-xl-8">

        <div class="card" style="color: #4B515D; border-radius: 35px;">
          <div class="card-body p-4">

            <div class="d-flex">
              <h6 class="flex-grow-1"><?php echo $donnee->name; ?></h6>
              <h6><?php echo $date; ?></h6>
            </div>

            <div class="d-flex flex-column text-center mt-5 mb-4">
              <h6 class="display-4 mb-0 font-weight-bold" style="color: #1C2331;"> <?php echo $donnee->main->temp; ?> °C </h6>
              <span class="small" style="color: #868B94">
              <?php echo ucwords($donnee->weather[0]->description); ?>
              </span>
            </div>

            <div class="d-flex align-items-center">
              <div class="flex-grow-1" style="font-size: 1rem;">
                <div><i class="wind" style="color: #868B94;"></i>
                 <span class="taille">
                    <?php echo $donnee->wind->speed; ?> km/h
                  </span>
                </div>
                <div><i class="humidity " style="color: #868B94;"></i>
                 <span class="taille">
                     <?php echo $donnee->main->humidity; ?>% 
                 </span>
                </div>
                <div><i class="pressure" style="color: #868B94;"></i>
                 <span class="taille">
                    <?php echo $donnee->main->pressure; ?> h 
                 </span>
                </div>
              </div>
              <div>
              <img src="https://openweathermap.org/themes/openweathermap/assets/vendor/owm/img/widgets/<?php echo $donnee->weather[0]->icon; ?>.png" alt="Weather widget" width="100px">

              </div>
            </div>

          </div>
        </div>

      </div>
    </div>

<?php
        // WordPress core after_widget hook (toujours appelé) )
        echo $after_widget;
    }
}
// Enregistrer le widget
function enregistrer_sunu_widget()
{
    register_widget('Sunu_Meteo_Widget');
}
add_action('widgets_init', 'enregistrer_sunu_widget');
add_action( 'wp_enqueue_scripts', 'widget_style');