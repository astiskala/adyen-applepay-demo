<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Adyen Checkout samples</title>
    <link rel="stylesheet" href="https://checkoutshopper-test.adyen.com/checkoutshopper/sdk/3.18.2/adyen.css">
</head>
<body>
    <div class="container container--full-width">
        <div class="main">
            <div class="checkout-container">
                <div class="payment-method">
                    <div id="dropin-container">
                        <!-- Drop-in will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://checkoutshopper-test.adyen.com/checkoutshopper/sdk/3.18.2/adyen.js"></script>
    <script>
      const getPaymentMethods = function getPaymentMethods() {
          return fetch(`paymentMethods.php`, {
              headers: {
                  'Accept': 'application/json, text/plain, */*',
                  'Content-Type': 'application/json',
              },
          }).then((response) => response.json());
      };

      const makePayment = function makePayment(data) {
          return fetch(`payments.php`, {
              method: 'POST',
              headers: {
                  'Accept': 'application/json, text/plain, */*',
                  'Content-Type': 'application/json',
              },
              body: JSON.stringify(data),
          }).then((response) => response.json());
      };

      const loadDropIn = function loadDropIn() {
          getPaymentMethods().then((paymentMethodsResponse) => {
              const checkout = new AdyenCheckout({
                  paymentMethodsResponse: paymentMethodsResponse,
                  clientKey: '<?=getenv('CHECKOUT_CLIENTKEY') ?>',
                  locale: 'en-US',
                  environment: 'test',
                  onSubmit: (state, dropin) => {
                      // TODO
                  },
                  onAdditionalDetails: (state, dropin) => {
                      // TODO
                  },
              });

              const paymentMethodsConfiguration = {
                  applepay: {
                      amount: 101,
                      currencyCode: 'EUR',
                      countryCode: 'NL',
                      onChange: (state) => {
                          makePayment(state.data)
                              .then((response) => {
                                  if (response.action) {
                                      dropin.handleAction(response.action);
                                  } else if (response.resultCode) {
                                      dropin.setStatus('success', {
                                          message: response.resultCode
                                      });
                                  } else if (response.message) {
                                      dropin.setStatus('success', {
                                          message: response.message
                                      });
                                  }
                              })
                              .catch((error) => {
                                  dropin.setStatus('error');
                              });
                      },
                  }
              };

              dropin = checkout
                  .create('dropin', {
                      paymentMethodsConfiguration,
                      onError: (state, component) => {
                          console.log('onError', state);
                      },
                  })
                  .mount('#dropin-container');
          });
      };

      loadDropIn();
    </script>
</body>
</html>
