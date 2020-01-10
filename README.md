Shortcode
==============

It works by `listening the response event` so the content which output from shortcodes is embedded right before a request returns a response.

Therefore, you can use the shortcode in anywhere and how many you want. E.g in the twig files, blocks, in translation files, in texts that are stored in database etc...

**It only accepts responses from the Carmignac and Sonata Controllers.**

#### The Syntax:

    %%your_short_code%%

It also accepts to use parameters which is a string json object but **only accepts one**.

    %%your_short_code {"name": "your name", "email": "your email"}%%

<em>The spaces in a shortcode are not important</em>.


#### Implemented shortcodes:

    %% policy_link %%

#### To create new shortcode:

   1. Create a new class in the `CoreBundle/Shortcode` directory, the name is not important here as long as it implements the `ShortcodeInterface`. But should follow the format: `yourShortcodeName` + `Shortcode`. Ex: PolicyLinkShortcode.
        ```
            interface ShortcodeInterface
            {
                /**
                 * Provide a tag name for short code
                 *
                 * @return string
                 */
                public function getTag(): string;
            
                /**
                 * @param array $parameters
                 *
                 * @return string
                 */
                public function output(array $parameters = []): string;
            }
        ```

   2. The value that returns from the method `getTag` method is used to rule the tag name of a shortcode:

        ```   
            ...
              
            const TAG = 'policy_link';
        
            /**
             * {@inheritdoc}
             */
            public function getTag(): string
            {
                return static::TAG;
            }
            
            ...
        ```
   3. Put your business code in the `output` method.

   4. Register that class as service and tag it with `carmignac.short_code`.

#### Notice:
  - It cannot handle a submitted form logical part.

  - The output has not been cached yet.

