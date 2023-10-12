<?php

$args = array(
    'numberposts' => -1,
    'post_type' => 'project',  // Ändra till korrekt cpt
    'orderby' => 'title',
    'order' => 'ASC',
);

$projects = get_posts($args);

if ($projects):
    $taxonomies = get_taxonomies();

    ?>

    <div class="portfolio-wrapper">
         
        <div class="filter-nav">

            <?php
            foreach ($taxonomies as $tax):
               
                // Lägg till / Ta bort taxonomies man ej vill visa ut
                if ($tax == 'category' || $tax == 'post_tag' || $tax == 'nav_menu' || $tax == 'link_category' || $tax == 'post_format' || $tax == 'wp_theme' || $tax == 'wp_template_part_area') {
                    continue;
                }
                //


                $nice_tax= str_replace('_', ' ', $tax); // delete " _ "

                ?>

                <label for="<?php echo $tax?>"><?php echo ucfirst($nice_tax); ?></label>
           
                <select onchange="applyFilters()" class="form-select form-select-lg mb-3 select" name="<?php echo $tax; ?>" aria-label=".form-select-lg example" id="<?php echo $tax?>">

                    <option value="">Visa alla</option> <!--- Visar ut tax som titel i selector --->

                    <?php
                    $terms = get_terms(array('taxonomy' => $tax));
                    foreach ($terms as $term):
                        ?>

                        <option value="<?php echo $term->slug; ?>"><?php echo $term->name; ?></option>  <!--- visar ut varje term i respektive tax --->

                    <?php endforeach; ?>

                </select>

            <?php endforeach; ?>


            <!-- Loopa igenom varje post i vald cpt -->
            <!-- Allt innehåll här går att redigera. För att filtreringen ska fungera måste class="<php echo $active_terms_class; ?> , det måste även finnas en konstant klass, tex projekt. denna måste hämtas av hiddenElems
            -->

            <?php 
            $client_args = array(
                'numberposts' => -1,
                'post_type' => 'clients',  // Ändra till korrekt cpt
                'orderby' => 'title',
                'order' => 'ASC',
            );

            $clients = get_posts($client_args);

            if ($clients):
                ?>
                <label for="Klienter">Klienter</label>
                <select onchange="applyFilters()" name="Klienter" class="form-select form-select-lg mb-3 select" aria-label=".form-select-lg example" id="clients">
                    <option value="">Visa alla</option><?php
                    foreach( $clients as $client):?>

                        <option value="<?php echo $client->ID; ?>"><?php echo $client->post_title; ?></option>  <!--- visar ut varje term i respektive tax --->

                    <?php endforeach;

                ?></select> <?php

            endif;?>

        </div>

        <div class="projekt-list">
            <?php
            foreach ($projects as $project):
                $title = $project->title;

                // Hämta alla aktiva taxar för varje post
                $active_taxonomies = get_object_taxonomies($project->post_type);
                $active_terms_class = '';

                foreach ($active_taxonomies as $active_taxonomy) {
                    $active_terms = get_the_terms($project->ID, $active_taxonomy);
                    $active_terms = is_array($active_terms) ? $active_terms : array();

                    // Ändra array till string för att det ska bli rätt när man tilldelar klasser
                    foreach ($active_terms as $active_term) {
                        $active_terms_class .= $active_term->slug . ' ';
                    }
                }
                $clients = get_field('client', $project->ID);
                ?>
            
                <a class="projekt <?php echo $active_terms_class; foreach($clients as $client): echo $client->ID; endforeach;?>" style="text-decoration:none;" href="<?php echo $project->guid; ?>">
                    <div class="projekt-card">
                        <?php // echo wp_get_attachment_image($img);?>
                        <div class="projekt-title">
                            <?php echo $title ?>
                        </div>
                    </div>
                </a>

            <?php endforeach; ?>

        </div>
    </div>


    <!--  -->




    <script>
        const allSelects = document.querySelectorAll('.select'); // Gemensam klass för alla options i select
        const hiddenElems = document.querySelectorAll('.projekt'); // Gemensamt klassnman till alla poster

        function applyFilters() {
            const filters = {};

            allSelects.forEach(function (select) {
                if (select.value) {
                    filters[select.name] = select.value;
                }
            });

            hiddenElems.forEach(function (element) {
                let display = true;

                Object.keys(filters).forEach(function (filterName) {
                    const filterValue = filters[filterName];

                    if (filterValue && !element.classList.contains(filterValue)) {
                        display = false;
                    }
                });

                if (display) {
                    element.classList.remove('hidden');
                } else {
                    element.classList.add('hidden');
                }
            });
        }
    </script>

<?php endif; ?>
