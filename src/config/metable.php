<?php

return [

    /*
	 *
     * all meta data are stored in meta table by default.
     * you can change default table and customize its name.
     * you can also define other tables. these tables will
     * be migrated when you call `migrate` artisan command.
     *
     * you can use 'metaTable' property to specify
     * the meta table for each model
     *
     */

    'tables' => [

        // default table for all models

        'default' => 'meta',

        // custom tables list

        'custom'  => [
            
            // example : 'posts_meta' , 'users_meta'

        ],
    ]
];