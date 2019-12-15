package kr.ac.sejong.gotgan

import android.annotation.TargetApi
import android.icu.util.Calendar
import android.os.Build
import android.os.Bundle
import android.text.format.DateFormat.format
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Toast
import androidx.fragment.app.DialogFragment
import com.appeaser.sublimepickerlibrary.SublimePicker
import com.appeaser.sublimepickerlibrary.datepicker.SelectedDate
import com.appeaser.sublimepickerlibrary.helpers.SublimeListenerAdapter
import com.appeaser.sublimepickerlibrary.helpers.SublimeOptions
import com.appeaser.sublimepickerlibrary.recurrencepicker.SublimeRecurrencePicker
import kr.ac.sejong.gotgan.Main.MainActivity_User.Companion.finishDateEdit
import kr.ac.sejong.gotgan.Main.MainActivity_User.Companion.rentAblePeriod
import kr.ac.sejong.gotgan.Main.MainActivity_User.Companion.startDateEdit
import java.time.LocalDate
import java.time.format.DateTimeFormatter

class SublimePickerDialogFragment : DialogFragment() {


    override fun onCreateView(inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?): View? {

        var mListener = object : SublimeListenerAdapter() {

            override fun onCancelled() {
                // Handle click on `Cancel` button
                dismiss()
            }

            @TargetApi(Build.VERSION_CODES.O)
            override fun onDateTimeRecurrenceSet(sublimeMaterialPicker: SublimePicker?, selectedDate: SelectedDate?, hourOfDay: Int, minute: Int, recurrenceOption: SublimeRecurrencePicker.RecurrenceOption?, recurrenceRule: String?) {

                /*
                recurrenceRule?.let{
                    // Do something with recurrenceRule
                }

                recurrenceOption?.let {
                    // Do something with recurrenceOption
                    // Call to recurrenceOption.toString() to get recurrenceOption as a String
                }
                 */
                //yearStr = selectedDate!!.startDate.get(Calendar.YEAR).toString()
               //monthStr = selectedDate.startDate.get(Calendar.MONTH).toString()
                //dayStr = selectedDate.startDate.get(Calendar.DATE).toString()
                //dayStr = selectedDate.startDate.get(Calendar.DATE).toString()
                var monthStr = (selectedDate!!.startDate.get(Calendar.MONTH) + 1).toString()
                if (monthStr.length == 1) monthStr = "0$monthStr"
                var dayStr = selectedDate.startDate.get(Calendar.DATE).toString()
                if (dayStr.length == 1) dayStr = "0$dayStr"
                startDateEdit!!.setText(selectedDate!!.startDate.get(Calendar.YEAR).toString() + "-" + monthStr + "-" + dayStr)

                //Log.d("dateTest", (LocalDate.parse((selectedDate.startDate.get(Calendar.YEAR).toString() + "-" + monthStr + "-" + dayStr), DateTimeFormatter.ISO_DATE)).plusDays(1).toString())
                finishDateEdit!!.setText((LocalDate.parse((selectedDate.startDate.get(Calendar.YEAR).toString() + "-" + monthStr + "-" + dayStr),
                    DateTimeFormatter.ISO_DATE)).plusDays(rentAblePeriod!!).toString())

                dismiss()
            }
        }

        var sublimePicker = SublimePicker(context)
        var sublimeOptions = SublimeOptions() // This is optional
        sublimeOptions.setDisplayOptions(SublimeOptions.ACTIVATE_DATE_PICKER) // I only want the recurrence picker, not the date/time pickers.
        sublimePicker.initializePicker(sublimeOptions,mListener)
        return sublimePicker

    }
}