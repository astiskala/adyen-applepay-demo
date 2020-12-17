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
                <h1>Drop-in</h1>
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
                  environment: 'test',
                  clientKey: '<?=getenv('CHECKOUT_CLIENTKEY') ?>',
                  paymentMethodsResponse: paymentMethodsResponse,
                  locale: 'en-US',
              });

              const paymentMethodsConfiguration = {
                  applepay: {
                      amount: 100,
                      currencyCode: 'USD',
                      countryCode: 'AU',
                      supportedNetworks: ['visa', 'masterCard', 'amex', 'discover', 'maestro', 'jcb'],
                      merchantCapabilities: ['supports3DS'],
                      onSubmit: (state, component) => {
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
                      amount: {
                          currency: 'USD',
                          value: 100
                      },
                      onSubmit: (state, component) => {
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
                      onAdditionalDetails: (state, component) => {
                          // TODO
                      },
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
