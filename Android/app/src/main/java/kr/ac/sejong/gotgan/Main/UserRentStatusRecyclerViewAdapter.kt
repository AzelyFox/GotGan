package kr.ac.sejong.gotgan.Main

import android.app.ProgressDialog
import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.appcompat.widget.AppCompatTextView
import androidx.recyclerview.widget.RecyclerView
import com.google.android.material.button.MaterialButton
import kr.ac.sejong.gotgan.R

class UserRentStatusRecyclerViewAdapter(_context : Context, _userRentStatusArrayList : ArrayList<UserRentStatusData>) : RecyclerView.Adapter<UserRentStatusRecyclerViewAdapter.ViewHolder>() {

    private var userRentStatusArrayList : ArrayList<UserRentStatusData>? = null
    private var context : Context? = null

    init {
        context = _context
        userRentStatusArrayList = _userRentStatusArrayList
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        return ViewHolder(LayoutInflater.from(parent.context).inflate(R.layout.user_rent_status_item, parent, false))
    }

    override fun getItemCount(): Int {
        return userRentStatusArrayList!!.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.userRentStatusItemNameTextView.text = userRentStatusArrayList!![position].productName
        holder.userRentStatusItemRentingTextView.text = userRentStatusArrayList!![position].rentStatus

        holder.userRentStatusItemRentDateValueTextView.text = userRentStatusArrayList!![position].rentStartDate

        holder.userRentStatusItemRentPlanDateValueTextView.text = userRentStatusArrayList!![position].rentEndPlanDate

        //if (userRentStatusArrayList!![position].rentStatus != "완료됨")  {
        //    holder.userRentStatusItemRentFinishDateTextView.visibility = View.GONE
        //    holder.userRentStatusItemRentFinishDateValueTextView.visibility = View.GONE
        //}
        holder.userRentStatusItemRentFinishDateValueTextView.text = userRentStatusArrayList!![position].rentEndFinnishDate
    }

    inner class ViewHolder (view : View) : RecyclerView.ViewHolder(view) {
        var userRentStatusItemNameTextView : AppCompatTextView = view.findViewById(R.id.user_rent_status_item_name_textView)
        var userRentStatusItemRentingTextView : AppCompatTextView = view.findViewById(R.id.user_rent_status_item_renting_textView)

        var userRentStatusItemRentDateValueTextView : AppCompatTextView = view.findViewById(R.id.user_rent_status_item_rent_date_value_textView)
        var userRentStatusItemRentPlanDateValueTextView : AppCompatTextView = view.findViewById(R.id.user_rent_status_item_rent_plan_date_value_textView)

        //var userRentStatusItemRentFinishDateTextView : AppCompatTextView = view.findViewById(R.id.user_rent_status_item_rent_finish_date_textView)
        var userRentStatusItemRentFinishDateValueTextView : AppCompatTextView = view.findViewById(R.id.user_rent_status_item_rent_finish_date_value_textView)


    }
}
