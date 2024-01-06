package com.example.motionui;
import android.content.Context;

import androidx.security.crypto.EncryptedSharedPreferences;
import androidx.security.crypto.MasterKeys;
import android.util.Log;

public class motionUIEncryptedSharedPreferences {

    String masterKeyAlias = null;
    EncryptedSharedPreferences sharedPreferences = null;

    /**
     *  Constructor
     */
    motionUIEncryptedSharedPreferences(Context context) {
        try {
            String masterKeyAlias = null;
            masterKeyAlias = MasterKeys.getOrCreate(MasterKeys.AES256_GCM_SPEC);

            /**
             *  Initializing encrypted shared preferences and passing key to it.
             */
            this.sharedPreferences = (EncryptedSharedPreferences) EncryptedSharedPreferences.create(
                /**
                 *  Passing a file name to share a preferences
                 */
                "motionUI_shared_preferences",
                masterKeyAlias,
                context,
                EncryptedSharedPreferences.PrefKeyEncryptionScheme.AES256_SIV,
                EncryptedSharedPreferences.PrefValueEncryptionScheme.AES256_GCM
            );
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    /**
     *  Get value from SharedPreferences by keyname
     */
    public String get(String keyname) {
        String value = null;

        value = this.sharedPreferences.getString(keyname, "");

        Log.e("motionUI", "Getting value " + value + " from shared preferences");
        /**
         *  Return found value
         */
        return value;
    }

    /**
     *  Save value to SharedPreferences
     */
    public void set(String keyname, String value) {
        try {
            /**
             *  Save username and password to shared preferences
             */
            this.sharedPreferences.edit().putString(keyname, value).apply();
        } catch (Exception e) {
            e.getMessage();
        }
    }

    /**
     *  Return true if keyname exists in SharedPreferences
     */
    public Boolean exists(String keyname) {
        if (this.sharedPreferences.contains(keyname)) {
            return true;
        }

        return false;
    }
}