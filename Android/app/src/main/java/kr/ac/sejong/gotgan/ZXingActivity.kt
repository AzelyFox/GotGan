package kr.ac.sejong.gotgan

import android.graphics.Color
import com.journeyapps.barcodescanner.CaptureActivity
import android.graphics.Color.parseColor
import android.os.AsyncTask
import android.view.WindowManager
import android.widget.LinearLayout
import android.widget.TextView
import android.view.ViewGroup
import android.os.Bundle
import android.util.Log
import androidx.annotation.Nullable
import java.lang.Exception


class ZXingActivity : CaptureActivity() {

    public override fun onCreate(@Nullable savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        val layoutParams = LinearLayout.LayoutParams(
            ViewGroup.LayoutParams.MATCH_PARENT,
            ViewGroup.LayoutParams.MATCH_PARENT
        )

        val title_view = TextView(this)
        title_view.layoutParams =
            LinearLayout.LayoutParams(
                WindowManager.LayoutParams.WRAP_CONTENT,
                WindowManager.LayoutParams.WRAP_CONTENT
            )
        title_view.setBackgroundColor(Color.parseColor("#00FFFFFF"))
        title_view.setPadding(150, 100, 100, 100)
        title_view.setTextColor(resources.getColor(android.R.color.white))
        title_view.textSize = 30f
        title_view.text = "바코드 입력 화면"

        this.addContentView(title_view, layoutParams)
    }
}