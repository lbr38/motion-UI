package app.motionui.android;

import android.view.View;
import android.webkit.WebChromeClient;
import android.webkit.WebView;
import android.widget.FrameLayout;
import androidx.appcompat.app.AppCompatActivity;

public class FullScreenWebChromeClient extends WebChromeClient {
    private AppCompatActivity activity;
    private View customView;
    private FrameLayout customViewContainer;
    private CustomViewCallback customViewCallback;
    private WebView webView;

    public FullScreenWebChromeClient(AppCompatActivity activity, WebView webView) {
        this.activity = activity;
        this.webView = webView;
        this.customViewContainer = activity.findViewById(R.id.fullscreen_container);
    }

    @Override
    public void onShowCustomView(View view, CustomViewCallback callback) {
        if (customView != null) {
            callback.onCustomViewHidden();
            return;
        }

        // Add the custom view for full screen
        customViewContainer.addView(view, FrameLayout.LayoutParams.MATCH_PARENT);
        customViewContainer.setVisibility(View.VISIBLE);
        customView = view;
        customViewCallback = callback;

        // Hide the main WebView
        webView.setVisibility(View.GONE);

        activity.getWindow().getDecorView().setSystemUiVisibility(
                View.SYSTEM_UI_FLAG_FULLSCREEN |
                View.SYSTEM_UI_FLAG_HIDE_NAVIGATION |
                View.SYSTEM_UI_FLAG_IMMERSIVE_STICKY
        );

        if (activity instanceof MainActivity) {
            ((MainActivity) activity).setFullScreen(true);
        }
    }

    @Override
    public void onHideCustomView() {
        if (customView == null) {
            return;
        }

        // Remove the custom view and restore the main WebView
        customViewContainer.removeView(customView);
        customViewContainer.setVisibility(View.GONE);
        customView = null;
        customViewCallback.onCustomViewHidden();

        webView.setVisibility(View.VISIBLE);

        activity.getWindow().getDecorView().setSystemUiVisibility(View.SYSTEM_UI_FLAG_VISIBLE);

        if (activity instanceof MainActivity) {
            ((MainActivity) activity).setFullScreen(false);
        }
    }
}