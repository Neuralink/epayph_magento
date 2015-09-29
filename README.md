# epayph-magento

Epay.ph's official Off-Site Gateway extension for Magento Community Edition.

## Version

0.0.1

## Requirements

- [Magento Community Edition](http://www.magentocommerce.com/)

## Compatibility

* Magento Community Edition versions >= 1.6

## Installation


### Automatic installation:

  Install our extension from [Magento Connect](http://www.magentocommerce.com/magento-connect/epayph.html)

### Manual installation:

  Copy the files in the /app folder to your Magento's /app folder. Then, activate the Epay.ph Extension from the backend admin panel.

### Setup:

  The Webhook feature allows the module to listen for Epay.ph transaction updates and update their corresponding order statuses automatically.

  To set that up, enable the Webhook feature for your [Epay.ph application](https://www.epayph.com/applications) and set the 'All URL' to:

  >http://[YOUR DOMAIN HERE]/index.php/epayphPaymentModule/payment/webhook

### Changelog

## Support

- Epay.ph API &lt;api@epay.ph&gt;

## References / Documentation

http://developers.epayph.com/

## License

(The MIT License)

Copyright (c) 2015 Epay.ph &lt;support@epayph.com&gt;

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
'Software'), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
