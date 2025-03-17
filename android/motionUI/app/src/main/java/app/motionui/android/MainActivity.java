package app.motionui.android;

import androidx.appcompat.app.AppCompatActivity;
import android.widget.Toast;
import android.app.DownloadManager;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.content.res.Configuration;
import android.net.Uri;
import android.os.Environment;
import android.os.Bundle;
import android.os.Build;
import android.util.Log;
import android.view.KeyEvent;
import android.webkit.DownloadListener;
import android.webkit.JavascriptInterface;
import android.webkit.WebChromeClient;
import android.webkit.WebResourceRequest;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.webkit.CookieManager;
import android.Manifest;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;
import androidx.annotation.NonNull;

/**
 *  MainActivity
 *  This is the main activity of the app (motionUI main page)
 */
public class MainActivity extends AppCompatActivity {
    private static final int STORAGE_PERMISSION_REQUEST_CODE = 1001;
    private WebView webView;
    private String url;
    private boolean isFullScreen = false;

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
         *  Check that the app has the necessary permissions to access the storage
         *  This is required to download files (videos and images from motion)
         */
        checkAndRequestStoragePermissions();

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

        /**
         *  Use custom WebChromeClient to enable features like full-screen video
         */
        webView.setWebChromeClient(new FullScreenWebChromeClient(this, webView));

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
                     *  Only do this if:
                     *  - there is no <div> with id="login-error" on the login page, which indicates that the login was already attempted and failed
                     *  - there is no cookie 'logout' (which means that the user as logout on purpose and may not want to login automatically again)
                     */
                    view.evaluateJavascript("if (document.querySelector('#login-form') != null) {"
                        + "if (document.querySelector('#login-error') == null) {"
                        + "var logoutCookie = document.cookie.split('; ').find(row => row.startsWith('logout='));"
                        + "if (logoutCookie == null) {"
                        + "document.querySelector('form[method=post]').submit();"
                        + "}"
                        + "}"
                        + "}" , null);
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
         *  Permit download of video files
         */
        webView.setDownloadListener(new DownloadListener() {
            @Override
            public void onDownloadStart(String url, String userAgent, String contentDisposition, String mimeType, long contentLength) {
                String fileName = "";

                /**
                 *  Try to extract the file name from the URL
                 *  The URL should contain the file name as a query parameter, like this: http://example.com/media.php?id=xxxxx&filename=myfile.mp4
                 */
                try {
                    Uri uri = Uri.parse(url);
                    fileName = uri.getQueryParameter("filename");
                } catch (Exception e) {
                    // Notify the user that the download failed because the file name could not be extracted
                    Toast.makeText(getApplicationContext(), "Download failed: error while extracting file name from URL", Toast.LENGTH_LONG).show();
                }

                Log.d("Download", "Downloading file: " + fileName);

                // Check that the file name is not empty
                if (fileName == null || fileName.isEmpty()) {
                    // Notify the user that the download failed because the file name could not be extracted
                    Toast.makeText(getApplicationContext(), "Download failed: could not extract file name from URL", Toast.LENGTH_LONG).show();
                    return;
                }

                /**
                 *  Start the download
                 */
                DownloadManager.Request request = new DownloadManager.Request(Uri.parse(url));
                request.setMimeType(mimeType);
                String cookies = CookieManager.getInstance().getCookie(url);
                request.addRequestHeader("cookie", cookies);
                request.addRequestHeader("User-Agent", userAgent);
                request.setDescription("Downloading file...");
                request.setTitle(fileName);
                request.allowScanningByMediaScanner();
                request.setNotificationVisibility(DownloadManager.Request.VISIBILITY_VISIBLE_NOTIFY_COMPLETED);
                request.setDestinationInExternalPublicDir(Environment.DIRECTORY_DOWNLOADS, fileName);

                DownloadManager downloadManager = (DownloadManager) getSystemService(DOWNLOAD_SERVICE);
                downloadManager.enqueue(request);
            }
        });
     
        /**
         *  Load motionUI URL in the WebView
         *  Restore webview state if it was saved (e.g. when the device is rotated)
         *  Otherwise, load the URL
         */
        if (savedInstanceState != null) {
            webView.restoreState(savedInstanceState);
            isFullScreen = savedInstanceState.getBoolean("isFullScreen", false);
        } else {
            // Load the URL
            webView.loadUrl(url);
        }
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

    @Override
    protected void onSaveInstanceState(@NonNull Bundle outState) {
        super.onSaveInstanceState(outState);
        webView.saveState(outState);
        outState.putBoolean("isFullScreen", isFullScreen);
    }

    @Override
    public void onConfigurationChanged(@NonNull Configuration newConfig) {
        super.onConfigurationChanged(newConfig);
    }

    public void setFullScreen(boolean isFullScreen) {
        this.isFullScreen = isFullScreen;
    }

    /**
     *  Check that the app has the necessary permissions to access the storage
     */
    private void checkAndRequestStoragePermissions() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            // Android 13 and higher: request specific media permissions
            if (ContextCompat.checkSelfPermission(this, Manifest.permission.READ_MEDIA_IMAGES) != PackageManager.PERMISSION_GRANTED ||
                ContextCompat.checkSelfPermission(this, Manifest.permission.READ_MEDIA_VIDEO) != PackageManager.PERMISSION_GRANTED ||
                ContextCompat.checkSelfPermission(this, Manifest.permission.READ_MEDIA_AUDIO) != PackageManager.PERMISSION_GRANTED) {
                
                ActivityCompat.requestPermissions(this,
                        new String[]{
                                Manifest.permission.READ_MEDIA_IMAGES,
                                Manifest.permission.READ_MEDIA_VIDEO,
                                Manifest.permission.READ_MEDIA_AUDIO
                        },
                        STORAGE_PERMISSION_REQUEST_CODE);
            }
        } else if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
            // Android 10 to Android 12: use READ_EXTERNAL_STORAGE
            if (ContextCompat.checkSelfPermission(this, Manifest.permission.READ_EXTERNAL_STORAGE) != PackageManager.PERMISSION_GRANTED) {
                ActivityCompat.requestPermissions(this,
                        new String[]{Manifest.permission.READ_EXTERNAL_STORAGE},
                        STORAGE_PERMISSION_REQUEST_CODE);
            }
        } else {
            // Android 9 and earlier: WRITE_EXTERNAL_STORAGE is required
            if (ContextCompat.checkSelfPermission(this, Manifest.permission.WRITE_EXTERNAL_STORAGE) != PackageManager.PERMISSION_GRANTED) {
                ActivityCompat.requestPermissions(this,
                        new String[]{Manifest.permission.WRITE_EXTERNAL_STORAGE},
                        STORAGE_PERMISSION_REQUEST_CODE);
            }
        }
    }

    /**
     *  This method is called after the user's response to the permissions
     */
    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
        if (requestCode == STORAGE_PERMISSION_REQUEST_CODE) {
            boolean allPermissionsGranted = true;
            for (int result : grantResults) {
                if (result != PackageManager.PERMISSION_GRANTED) {
                    allPermissionsGranted = false;
                    break;
                }
            }
            if (!allPermissionsGranted) {
                // One or more permissions were denied
                Toast.makeText(this, "Write permission is denied. You will not be able to download files.", Toast.LENGTH_LONG).show();
            }
        }
    }
}