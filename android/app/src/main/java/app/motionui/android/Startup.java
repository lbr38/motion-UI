package app.motionui.android;

import androidx.appcompat.app.AppCompatActivity;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.Response;
import java.io.IOException;
import android.widget.TextView;

/**
 *  Startup activity
 *  This is the first activity that is opened when the app is launched
 *  It contains a form to enter the URL of the motionUI server if it is not already saved in the app
 *  Then it redirects to the MainActivity (motionUI main page)
 */
public class Startup extends AppCompatActivity {
    private Button button;
    private EditText editText;
    private String url;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_startup);

        /**
         *  Instanciate encrypted shared preferences
         */
        motionUIEncryptedSharedPreferences encryptedSharedPrefs = new motionUIEncryptedSharedPreferences(getApplicationContext());

        /**
         *  If an URL is already saved in the app, retrieve it and open the MainActivity (motionUI main page)
         */
        if (encryptedSharedPrefs.exists("url")) {
            url = encryptedSharedPrefs.get("url");

            /**
             *  Fill the EditText with the saved URL
             */
            editText = findViewById(R.id.startupUrlEditText);
            editText.setText(url);

            /**
             *  Try to connect to the motionUI server
             */
            connect(url);
        }

        /**
         *  If there is no URL already saved in the app, display the form to enter the URL
         */

        /**
         *  Retrieve URL validate button by its Id
         */
        button = findViewById(R.id.startupUrlValidateBtn);

        /**
         *  Retrieve EditText by its Id
         */
        editText = findViewById(R.id.startupUrlEditText);
        
        /**
         *  Set a click listener on validate button
         */
        button.setOnClickListener(new View.OnClickListener() {
           @Override
            /**
             *  When button is clicked, retrieve URL from editText and open the MainActivity
             */
            public void onClick(View view) {

                /**
                 *  Retrieve URL from EditText
                 */
                url = editText.getText().toString();

                /**
                 *  Check that URL is not empty
                 */
                if (url.isEmpty()) {
                    // If URL is empty, print an error message
                    editText.setError("URL is required");

                    return;
                }

                /**
                 *  Check that URL starts with http:// or https://
                 */
                if (!url.startsWith("http://") && !url.startsWith("https://")) {
                    // If URL does not start with http:// or https://, print an error message
                    editText.setError("URL must start with http:// or https://");

                    return;
                }

                /**
                 *  Check that URL has a valid domain name (e.g. www.mydomain.net or mydomain.net)
                 */
                if (!url.matches("^(http|https)://.*\\..*")) {
                    // If URL does not have a valid domain name, print an error message
                    editText.setError("URL must have a valid domain name");

                    return;
                }

                /**
                 *  Save URL to local storage (shared preferences)
                 */
                encryptedSharedPrefs.set("url", url);

                /**
                 *  Try to connect to the motionUI server
                 */
                connect(url);
            }
        });
    }

    /**
     *  Open motionUI using passed URL
     */
    public void openMotionUI(String url) {
        /**
         *  Start a new intent to open the MainActivity (motionUI main page)
         */
        Intent intent = new Intent(Startup.this, MainActivity.class);

        /**
         *  Pass the URL to the MainActivity
         */
        intent.putExtra("url", url);

        /**
         *  Start the new activity (open motionUI main page)
         */
        startActivity(intent);
    }

    /**
     *  Check connection to the motionUI server
     */
    public void connect(String url) {
        TextView connectionStatus = findViewById(R.id.startupConnectionStatus);

        /**
         *  Try to connect to the motionUI server
         *  Start the connection in a separate thread
         */
        connectionStatus.setText("Connecting to " + url + "...");

        new Thread(() -> {
            OkHttpClient client = new OkHttpClient();
            Request request = new Request.Builder()
                    .url(url)
                    .build();
            try (Response response = client.newCall(request).execute()) {
                // If connection is successful, open motionUI web page
                if (response.isSuccessful()) {
                    openMotionUI(url);
                } else {
                    runOnUiThread(() -> {
                        // Print error
                        connectionStatus.setText("Connection failed: " + response.message());
                    });
                }
            } catch (IOException e) {
                runOnUiThread(() -> {
                    // Print error
                    connectionStatus.setText("Connection error: " + e.getMessage());
                });
            }
        }).start();
    }
}