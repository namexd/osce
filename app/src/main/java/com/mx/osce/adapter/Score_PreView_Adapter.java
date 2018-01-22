package com.mx.osce.adapter;

import java.util.ArrayList;
import java.util.HashMap;

import com.mx.osce.R;
import com.mx.osce.bean.GradePointBean_Net;
import com.mx.osce.util.Constant;
import com.mx.osce.util.Utils;

import android.content.Context;
import android.graphics.Color;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;

public class Score_PreView_Adapter extends BaseAdapter {
	Context context;
	private LayoutInflater mInflater;// 得到一个LayoutInfalter对象用来导入布局 /*构造函数*/
	private ArrayList<GradePointBean_Net> mData;
	private ArrayList<HashMap<String, String>> mArrayList = new ArrayList<HashMap<String, String>>();

	public Score_PreView_Adapter(Context context, ArrayList<GradePointBean_Net> data) {
		this.context = context;
		this.mInflater = LayoutInflater.from(context);
		this.mData = data;
		makeAdapterData();

	}

	public void makeAdapterData() {

		for (int i = 0; i < mData.size(); i++) {
			HashMap<String, String> map1 = new HashMap<String, String>();
			map1.put("name", mData.get(i).getSort());
			if (!mData.get(i).getTag().equalsIgnoreCase(Constant.NORMAL_TAG)) {
				map1.put("content", mData.get(i).getTitle());
			} else {
				map1.put("content", mData.get(i).getContent());
			}

			map1.put("type", "0");
			int total = 0;
			if (!mData.get(i).getTag().equalsIgnoreCase(Constant.NORMAL_TAG)) {
				total = Integer.parseInt(Utils.getSharedPrefrences(context, mData.get(i).getId()));
			} else {
				for (int j = 0; j < mData.get(i).getTest_term().size(); j++) {
					total += Integer.parseInt(mData.get(i).getTest_term().get(j).getScore());
				}
			}
			int Real = 0;
			map1.put("totlescore", "" + total);
			if (!mData.get(i).getTag().equalsIgnoreCase(Constant.NORMAL_TAG)) {
				// Real += (Integer.parseInt(mData.get(i).getScore()) -
				// Integer.parseInt(mData.get(i).getSubtract())) <= 0
				// ? 0
				// : (Integer.parseInt(mData.get(i).getScore()) -
				// Integer.parseInt(mData.get(i).getSubtract()));

				Real += Integer.parseInt(mData.get(i).getSubtract()) * (-1);

			} else {
				for (int j = 0; j < mData.get(i).getTest_term().size(); j++) {
					Real += Integer.parseInt(mData.get(i).getTest_term().get(j).getReal());
				}
			}
			map1.put("score", "" + Real);
			map1.put("totlescore", "" + total);
			mArrayList.add(map1);

			if (mData.get(i).getTag().equalsIgnoreCase(Constant.NORMAL_TAG)) {
				for (int j = 0; j < mData.get(i).getTest_term().size(); j++) {
					HashMap<String, String> map = new HashMap<String, String>();
					map.put("name", mData.get(i).getTest_term().get(j).getSort());
					map.put("content", mData.get(i).getTest_term().get(j).getContent());
					map.put("score", mData.get(i).getTest_term().get(j).getReal());
					map.put("type", "1");
					map.put("totlescore", mData.get(i).getTest_term().get(j).getScore());
					mArrayList.add(map);
				}
			}
		}

	}

	@Override
	public int getCount() {
		return mArrayList.size();// 返回数组的长度
	}

	@Override
	public Object getItem(int position) {
		return null;
	}

	@Override
	public long getItemId(int position) {
		return 0;
	}

	@Override
	public View getView(final int position, View convertView, ViewGroup parent) {
		ViewHolder holder;
		// 观察convertView随ListView滚动情况
		Log.v("Score_PreView_Adapter", "getView " + position + " " + convertView);

		convertView = mInflater.inflate(R.layout.score_preview_listitem, null);
		holder = new ViewHolder();
		/* 得到各个控件的对象 */
		holder.point = (TextView) convertView.findViewById(R.id.point);
		holder.content = (TextView) convertView.findViewById(R.id.content);
		holder.totlescore = (TextView) convertView.findViewById(R.id.totle_score);
		holder.score = (TextView) convertView.findViewById(R.id.score);
		convertView.setTag(holder);// 绑定ViewHolder对象

		/* 设置TextView显示的内容，即我们存放在动态数组中的数据 */
		if (mArrayList.get(position).get("name") != null)
			holder.point.setText(mArrayList.get(position).get("name"));
		if (mArrayList.get(position).get("content") != null)
			holder.content.setText(mArrayList.get(position).get("content"));
		if (mArrayList.get(position).get("score") != null)
			holder.score.setText(mArrayList.get(position).get("score"));
		if (mArrayList.get(position).get("totlescore") != null)
			holder.totlescore.setText(mArrayList.get(position).get("totlescore"));

		if (mArrayList.get(position).get("type").equalsIgnoreCase("0")) {
			convertView.setBackgroundColor(Color.parseColor("#eeeeee"));
			convertView.setPadding(10, 10, 10, 10);
		} else {
			convertView.setPadding(10, 10, 10, 10);
			convertView.setBackgroundColor(Color.parseColor("#ffffff"));
		}
		return convertView;
	}

	/* 存放控件 */
	public final class ViewHolder {

		public TextView point;
		public TextView content;
		public TextView totlescore;
		public TextView score;

	}

}
