package com.example.motionui;

import androidx.appcompat.app.AppCompatActivity;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;

import android.util.Log;

// Encrypted SharedPreferences
// import androidx.security.crypto.EncryptedSharedPreferences;
// import androidx.security.crypto.MasterKeys;

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
             *  Open motionUI using retrieved URL
             */
            openMotionUI(url);
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
                 *  Open motionUI using passed URL
                 */
                openMotionUI(url);
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
}