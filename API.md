# CDK API

You can also run the app by calling the api.

## Usage

You must send GET requests to https://lj4.wilke.xyz/example.api

Paramters are :

-   `lang`, one of :
    -   `la-VA` : Vatican, Latin
    -   `fr-FR`: France, French
    -   `en-US`: USA, English
-   `body`, the text to be syllabified

## Example

`curl -s 'https://lj4.wilke.xyz/example.api?lang=la-VA&body=Dómine'` will output `Dó-mi-ne`
