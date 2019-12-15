package kr.ac.sejong.gotgan.Main

import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageView
import androidx.appcompat.widget.AppCompatTextView
import androidx.cardview.widget.CardView
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.RecyclerView
import kr.ac.sejong.gotgan.R

class MasterProductListRecyclerViewAdapter(_context : Context, _productListArrayList : ArrayList<MasterProductListData>) : RecyclerView.Adapter<MasterProductListRecyclerViewAdapter.ViewHolder>() {

    private var productListArrayList : ArrayList<MasterProductListData>? = null
    private var context : Context? = null

    init {
        context = _context
        productListArrayList = _productListArrayList
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        return ViewHolder(LayoutInflater.from(parent.context).inflate(R.layout.product_list_item, parent, false))
    }

    override fun getItemCount(): Int {
        return productListArrayList!!.size
    }
    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.productNameTextView.text = productListArrayList!![position].groupName
        holder.canRentCountTextView.text = (productListArrayList!![position].groupCountAvailable!! - productListArrayList!![position].groupCountRent!!).toString()
        holder.alreadyRentCountTextView.text = productListArrayList!![position].groupCountRent.toString()
        holder.allStockCountTextView.text = ((productListArrayList!![position].groupCountAvailable!! - productListArrayList!![position].groupCountRent!!)
                + productListArrayList!![position].groupCountRent!!
                + productListArrayList!![position].groupCountUnavailable!!
                + productListArrayList!![position].groupCountBroken!!
                + productListArrayList!![position].groupCountRepair!!).toString()

        holder.cantUseCountTextView.text = productListArrayList!![position].groupCountUnavailable.toString()
        holder.brokenCountTextView.text = productListArrayList!![position].groupCountBroken.toString()
        holder.fixingCountTextView.text = productListArrayList!![position].groupCountRepair.toString()

        var arrowBoolean = true

        holder.cardView.setOnClickListener {
            if (arrowBoolean) {
                holder.arrowImageView.setImageDrawable(ContextCompat.getDrawable(context!!,R.drawable.up_arrow))
                arrowBoolean = false
            }
            else {
                holder.arrowImageView.setImageDrawable(ContextCompat.getDrawable(context!!, R.drawable.down_arrow))
                arrowBoolean = true
            }


            if (holder.cantUseCountTextView.visibility == View.VISIBLE) holder.cantUseCountTextView.visibility = View.GONE
            else holder.cantUseCountTextView.visibility = View.VISIBLE

            if (holder.cantUseTextView.visibility == View.VISIBLE) holder.cantUseTextView.visibility = View.GONE
            else holder.cantUseTextView.visibility = View.VISIBLE

            if (holder.brokenCountTextView.visibility == View.VISIBLE) holder.brokenCountTextView.visibility = View.GONE
            else holder.brokenCountTextView.visibility = View.VISIBLE

            if (holder.brokenTextView.visibility == View.VISIBLE) holder.brokenTextView.visibility = View.GONE
            else holder.brokenTextView.visibility = View.VISIBLE

            if (holder.fixingCountTextView.visibility == View.VISIBLE) holder.fixingCountTextView.visibility = View.GONE
            else holder.fixingCountTextView.visibility = View.VISIBLE

            if (holder.fixingTextView.visibility == View.VISIBLE) holder.fixingTextView.visibility = View.GONE
            else holder.fixingTextView.visibility = View.VISIBLE
        }
    }

    inner class ViewHolder (view : View) : RecyclerView.ViewHolder(view) {
        var productNameTextView : AppCompatTextView = view.findViewById(R.id.product_group_name_textView)
        var canRentCountTextView : AppCompatTextView = view.findViewById(R.id.product_list_can_rent_count_textView)
        var alreadyRentCountTextView : AppCompatTextView = view.findViewById(R.id.product_list_already_rent_count_textView)
        var allStockCountTextView : AppCompatTextView = view.findViewById(R.id.product_list_all_stock_count_textView)

        // detail
        var cantUseCountTextView : AppCompatTextView = view.findViewById(R.id.product_list_cant_use_count_textView)
        var cantUseTextView : AppCompatTextView = view.findViewById(R.id.product_list_cant_use_textView)
        var brokenCountTextView : AppCompatTextView = view.findViewById(R.id.product_list_broken_count_textView)
        var brokenTextView : AppCompatTextView = view.findViewById(R.id.product_list_broken_textView)
        var fixingCountTextView : AppCompatTextView = view.findViewById(R.id.product_list_fixing_count_textView)
        var fixingTextView : AppCompatTextView = view.findViewById(R.id.product_list_fixing_textView)

        var cardView : CardView = view.findViewById(R.id.product_list_cardView)
        var arrowImageView : ImageView = view.findViewById(R.id.product_list_arrow)

    }
}
