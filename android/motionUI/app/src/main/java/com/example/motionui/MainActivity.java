package com.example.motionui;

import androidx.appcompat.app.AppCompatActivity;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.KeyEvent;
import android.webkit.JavascriptInterface;
import android.webkit.WebChromeClient;
import android.webkit.WebResourceRequest;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.os.Build;
import android.webkit.CookieManager;

/**
 *  MainActivity
 *  This is the main activity of the app (motionUI main page)
 */
public class MainActivity extends AppCompatActivity {

    private WebView webView;
    private String url;
    // private Integer authTry = 0;

    /**
     *  JavaScript interface to retrieve username and password from the login form when the user submits it and save them in SharedPreferences
     */
    public class MyJavaScriptInterface {
        @SuppressWarnings("unused")
        @JavascriptInterface

        /**
         *  Save username and password in SharedPreferences after user has clicked on the login button
         */
        public void processContent(String username, String password) {
            /**
             *  Instanciate encrypted shared preferences
             */
            motionUIEncryptedSharedPreferences encryptedSharedPrefs = new motionUIEncryptedSharedPreferences(getApplicationContext());

            /**
             *  If username or password is empty, do not save them in SharedPreferences
             */
            if (!(username.length() >= 1 && password.length() >= 1)) {
                return;
            }

            /**
             *  Save username and password to shared preferences
             */
            encryptedSharedPrefs.set("username", username);
            encryptedSharedPrefs.set("password", password);
        }
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        /**
         *  Retrieve motionUI URL from Startup activity
         */
        url = getIntent().getStringExtra("url");

        /**
         *  Retrieve WebView by its Id
         */
        webView = findViewById(R.id.motionUIWebView);

        /**
         *  Enable JavaScript
         */
        webView.getSettings().setJavaScriptEnabled(true);
        webView.getSettings().setDomStorageEnabled(true);

        /**
         *  Enable zoom but hide the controls (zoom buttons)
         */
        webView.getSettings().setBuiltInZoomControls(true);
        webView.getSettings().setDisplayZoomControls(false);

        // For debugging:
        webView.setWebChromeClient(new WebChromeClient());

        /**
         *  Enable JavaScript interface "MyJavaScriptInterface()" to retrieve username and password from the login form when the user submits it and save them in SharedPreferences
         *  See MyJavaScriptInterface class above
         */
        webView.addJavascriptInterface(new MyJavaScriptInterface(), "INTERFACE");

        /**
         *  Enable third-party cookies
         */
        if (Build.VERSION.SDK_INT >= 21) {
            CookieManager.getInstance().setAcceptThirdPartyCookies(webView, true);
        } else {
            CookieManager.getInstance().setAcceptCookie(true);
        }
        
        /**
         *  Set a WebViewClient to open URL in the WebView
         */
        webView.setWebViewClient(new WebViewClient() {
            /**
             *  When page is finished loading
             */
            @Override
            public void onPageFinished(WebView view, String url) {
                String username = "";
                String password = "";
                // Integer authTry = 0;

                /**
                 *  Instanciate encrypted shared preferences
                 */
                motionUIEncryptedSharedPreferences encryptedSharedPrefs = new motionUIEncryptedSharedPreferences(getApplicationContext());

                /**
                 *  Retrieve username and password from SharedPreferences if they exist
                 */
                if (encryptedSharedPrefs.exists("username")) {
                    username = encryptedSharedPrefs.get("username");
                }
                if (encryptedSharedPrefs.exists("password")) {
                    password = encryptedSharedPrefs.get("password");
                }

                /**
                 *  Try to autofill the username and password if not empty (if they were retrieved from local storage)
                 */
                if (username.length() >= 1 && password.length() >= 1) {
                    view.evaluateJavascript("document.querySelector('[name=username]').value = '" + username + "';", null);
                    view.evaluateJavascript("document.querySelector('[name=password]').value = '" + password + "';", null);

                    /**
                     *  Autologin if this is the first time the user logs in (click on the login button)
                     */
                    // if (this.authTry == 0) {
                    //     view.evaluateJavascript("document.querySelector('[type=submit]').click();", null);
                    //     this.authTry++;
                    // }
                }

                /**
                 *  Inject JavaScript submit listener on the page, to retrieve the username and password when the login form is submitted
                 *  The listener will call the "processContent" method of the JavaScript interface "MyJavaScriptInterface()" and send the username and password as parameters to it in order to save them in SharedPreferences
                 */
                String javascriptSubmitListener = "$(document).on('submit','form[method=post]',function () {"
                                                + "INTERFACE.processContent("
                                                + "document.querySelector('[name=username]').value,"
                                                + "document.querySelector('[name=password]').value"
                                                + ");"
                                                + "});";
                view.evaluateJavascript(javascriptSubmitListener, null);
            }

            /**
             *  Open external link in the default browser
             */
            @Override
            public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest request) {
                final String targetUrl = request.getUrl().toString();

                /**
                 *  If the target URL contains the motionUI URL, open it in the WebView
                 */
                if (targetUrl.contains(url)) {
                    view.loadUrl(targetUrl);

                /**
                 *  Else open it in the default browser
                 */
                } else {
                    Intent intent = new Intent(Intent.ACTION_VIEW, request.getUrl());
                    startActivity(intent);
                }

                return true;
            }
        });

        /**
         *  Load motionUI URL in the WebView
         */
        webView.loadUrl(url);
    }

    /**
     *  Prevent the back-button from closing the app
     */
    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK && this.webView.canGoBack()) {
            this.webView.goBack();

            return true;
        }

        return super.onKeyDown(keyCode, event);
    }
}