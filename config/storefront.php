<?php

return [
    'brand' => [
        'name' => env('STOREFRONT_BRAND_NAME', 'AltaEsencia'),
        'shortName' => env('STOREFRONT_BRAND_SHORT_NAME', 'AE'),
        'tagline' => env('STOREFRONT_BRAND_TAGLINE', 'Estilo y Exclusividad'),
    ],

    'commerce' => [
        'whatsappNumber' => env('STOREFRONT_WHATSAPP_NUMBER', '59175540850'),
        'maxQuantityPerProduct' => (int) env('STOREFRONT_MAX_QUANTITY_PER_PRODUCT', 3),
    ],

    'categories' => [
        'clothing' => [
            'id' => 'clothing',
            'label' => 'Clothing',
            'navSubtitle' => 'Alta costura',
            'title' => 'Ropa de Alta Gama',
            'description' => 'Coleccion exclusiva de ropa de alto nivel de las mejores marcas',
            'buttonText' => 'VER ROPA',
            'path' => '/categoria/clothing',
            'image' => [
                'src' => 'https://images.pexels.com/photos/1661471/pexels-photo-1661471.jpeg',
                'alt' => 'Ropa de alta gama',
            ],
        ],
        'perfumes' => [
            'id' => 'perfumes',
            'label' => 'Perfumes',
            'navSubtitle' => 'Esencias selectas',
            'title' => 'Perfumes de Lujo',
            'description' => 'Aromas unicos de las marcas mas prestigiosas del mundo',
            'buttonText' => 'VER PERFUMES',
            'path' => '/categoria/perfumes',
            'image' => [
                'src' => 'https://images.pexels.com/photos/965989/pexels-photo-965989.jpeg',
                'alt' => 'Perfumes de lujo',
            ],
        ],
        'cosmetics' => [
            'id' => 'cosmetics',
            'label' => 'Cosmetics',
            'navSubtitle' => 'Belleza premium',
            'title' => 'Cosmeticos Premium',
            'description' => 'Productos de belleza premium con ingredientes de lujo',
            'buttonText' => 'VER COSMETICOS',
            'path' => '/categoria/cosmetics',
            'image' => [
                'src' => 'https://images.pexels.com/photos/2533266/pexels-photo-2533266.jpeg',
                'alt' => 'Cosmeticos premium',
            ],
        ],
    ],

    'home' => [
        'sections' => [
            ['id' => 'inicio', 'label' => 'Inicio'],
            ['id' => 'categorias', 'label' => 'Categorias'],
            ['id' => 'premium', 'label' => 'Premium'],
            ['id' => 'perfumes', 'label' => 'Perfumes'],
            ['id' => 'ofertas', 'label' => 'Ofertas'],
            ['id' => 'contacto', 'label' => 'Contacto'],
        ],
        'hero' => [
            'title' => "ESTILO &\nEXCLUSIVIDAD",
            'subtitle' => 'Encuentra ropa, perfumes y cosmeticos de las marcas mas exclusivas.',
            'ctaLabel' => 'Ver Coleccion',
            'ctaHref' => '/#categorias',
            'image' => [
                'src' => 'https://images.pexels.com/photos/5704720/pexels-photo-5704720.jpeg',
                'alt' => 'Modelo con perfume',
            ],
        ],
        'categoriesHeading' => [
            'title' => 'Descubre AltaEsencia',
            'subtitle' => 'Moda de Alto Nivel, Perfumes Exclusivos y Cosmeticos Selectos',
        ],
        'premiumCollection' => [
            'title' => 'Coleccion Premiuxm',
            'subtitle' => 'Seleccion Exclusiva de Alta Calidad',
            'description' => 'Prendas y accesorios curados para quienes buscan presencia, textura y exclusividad.',
            'ctaLabel' => 'Ver Coleccion',
            'ctaHref' => '/categoria/clothing',
            'images' => [
                ['src' => 'https://images.pexels.com/photos/3622622/pexels-photo-3622622.jpeg', 'alt' => 'Moda premium 1'],
                ['src' => 'https://images.pexels.com/photos/3394650/pexels-photo-3394650.jpeg', 'alt' => 'Moda premium 2'],
                ['src' => 'https://images.pexels.com/photos/3621104/pexels-photo-3621104.jpeg', 'alt' => 'Moda premium 3'],
                ['src' => 'https://images.pexels.com/photos/934070/pexels-photo-934070.jpeg', 'alt' => 'Moda premium 4'],
            ],
        ],
        'exclusivePerfumes' => [
            'title' => 'Perfumes Exclusivos',
            'subtitle' => 'Descubre aromas unicos de las marcas mas prestigiosas.',
            'description' => 'Fragancias con identidad propia, notas profundas y presencia duradera.',
            'ctaLabel' => 'Ver Perfumes',
            'ctaHref' => '/categoria/perfumes',
            'items' => [
                [
                    'id' => 'preview-noir',
                    'name' => 'AltaEsencia Noir',
                    'priceLabel' => '$299.99',
                    'featured' => true,
                    'image' => [
                        'src' => 'https://images.pexels.com/photos/965989/pexels-photo-965989.jpeg',
                        'alt' => 'AltaEsencia Noir',
                    ],
                ],
                [
                    'id' => 'preview-gold',
                    'name' => 'AltaEsencia Gold',
                    'priceLabel' => '$249.99',
                    'image' => [
                        'src' => 'https://images.pexels.com/photos/1190829/pexels-photo-1190829.jpeg',
                        'alt' => 'AltaEsencia Gold',
                    ],
                ],
            ],
        ],
        'promo' => [
            'badge' => 'Oferta Especial',
            'title' => 'Hasta 40% OFF',
            'subtitle' => 'En productos seleccionados de nuestra coleccion premium',
            'benefits' => [
                'Envio gratuito en compras superiores a $200',
                'Productos 100% originales garantizados',
            ],
            'primaryCtaLabel' => 'Comprar Ahora',
            'primaryCtaHref' => '/categoria/perfumes',
            'secondaryCtaLabel' => 'Ver Ofertas',
            'secondaryCtaHref' => '/#categorias',
            'image' => [
                'src' => 'https://images.pexels.com/photos/3762879/pexels-photo-3762879.jpeg',
                'alt' => 'Productos en promocion',
            ],
        ],
        'footer' => [
            'description' => 'Tu destino para moda exclusiva, perfumes de lujo y cosmeticos premium.',
            'categoryLinks' => [
                ['label' => 'Ropa de Alta Gama', 'href' => '/categoria/clothing'],
                ['label' => 'Perfumes de Lujo', 'href' => '/categoria/perfumes'],
                ['label' => 'Cosmeticos Premium', 'href' => '/categoria/cosmetics'],
            ],
            'informationLinks' => [
                ['label' => 'Sobre Nosotros', 'href' => '/#inicio'],
                ['label' => 'Coleccion Premium', 'href' => '/#premium'],
                ['label' => 'Ofertas', 'href' => '/#ofertas'],
            ],
            'legalLinks' => [
                ['label' => 'Politica de Privacidad', 'href' => '/#contacto'],
                ['label' => 'Terminos y Condiciones', 'href' => '/#contacto'],
            ],
            'contact' => [
                'address' => env('STOREFRONT_CONTACT_ADDRESS', 'Av. Exclusiva 123, Ciudad'),
                'phone' => env('STOREFRONT_CONTACT_PHONE', '+591 75540850'),
                'email' => env('STOREFRONT_CONTACT_EMAIL', 'info@altaesencia.com'),
            ],
            'copyright' => env('STOREFRONT_COPYRIGHT', '(c) 2026 AltaEsencia. Todos los derechos reservados.'),
        ],
    ],
];
