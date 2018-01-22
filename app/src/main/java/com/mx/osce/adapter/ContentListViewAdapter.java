package com.mx.osce.adapter;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.GregorianCalendar;
import java.util.TimeZone;

import com.hb.views.PinnedSectionListView.PinnedSectionListAdapter;
import com.mx.osce.R;
import com.mx.osce.bean.GradePointBean_Net;
import com.mx.osce.bean.Item;
import com.mx.osce.bean.PointTermBean;
import com.mx.osce.custom.CalendarCard;
import com.mx.osce.custom.CalendarCard.OnCellClickListener;
import com.mx.osce.custom.CustomDate;
import com.mx.osce.util.Constant;
import com.mx.osce.util.Utils;

import android.content.Context;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.EditText;
import android.widget.TextView;
import cn.pedant.SweetAlert.widget.SweetAlertDialog;
import cn.pedant.SweetAlert.widget.SweetAlertDialog.OnSweetClickListener;

public class ContentListViewAdapter extends ArrayAdapter<Item> implements PinnedSectionListAdapter {

	private LayoutInflater inflater = null;

	private ArrayList<GradePointBean_Net> inData;

	private ArrayList<Item> menuListData;

	private boolean showAnswer;
	private Context mContext;

	private long time_difference;// 本地时间余服务器时间差

	public GetScoreInterface mGetScoreInterface;

	public ContentListViewAdapter(Context context, int resource, ArrayList<GradePointBean_Net> data) {
		super(context, resource);
		mContext = context;
		this.inData = data;
		menuListData = new ArrayList<Item>();
		generateDataset(false);
		inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);

		long service_time = Long.parseLong(Utils.getSharedPrefrences(mContext, "service_time"));

		long client_time = Long.parseLong(Utils.getSharedPrefrences(mContext, "client_time"));
		time_difference = service_time - client_time;
	}

	public void generateDataset(boolean clear) {

		if (clear) {
			clear();
		}
		final int sectionsNumber = inData.size();
		int sectionPosition = 0;
		int listPosition = 0;

		for (int i = 0; i < sectionsNumber; i++) {
			if (null != inData.get(i).getTag() && (Constant.NORMAL_TAG).equalsIgnoreCase(inData.get(i).getTag())) {
				Item section = new Item(Item.SECTION, inData.get(i).getContent(), null);
				section.sectionPosition = sectionPosition++;
				section.listPosition = listPosition++;
				section.pointTermBeans = inData.get(i).getTest_term();
				section.id = inData.get(i).getId();
				this.add(section);
				menuListData.add(section);

				final int itemsNumber = section.pointTermBeans.size();
				if (itemsNumber > 0) {
					ArrayList<PointTermBean> test_term = section.pointTermBeans;
					for (int j = 0; j < itemsNumber; j++) {

						Item item = new Item(Item.ITEM_NORMAL, test_term.get(j).getContent(),
								test_term.get(j).getAnswer());
						item.sectionPosition = section.sectionPosition;
						item.id = test_term.get(j).getId();
						item.bean = test_term.get(j);
						item.positionOfParent = j;
						item.listPosition = listPosition++;
						add(item);
						menuListData.add(item);
					}
				}
			} else if (inData.get(i).getTag() != null
					&& inData.get(i).getTag().equalsIgnoreCase(Constant.SPECIAL_TAG)) {

				String title = inData.get(i).getTitle() == null ? "" : (inData.get(i).getTitle());

				Item section = new Item(Item.SECTION, title, null);
				section.sectionPosition = sectionPosition++;
				section.listPosition = listPosition++;
				section.id = inData.get(i).getId();
				this.add(section);
				menuListData.add(section);

				Item sectionSpecial = new Item(Item.ITEM_SPECIAL, inData.get(i).getTitle(), null);
				sectionSpecial.sectionPosition = section.sectionPosition;
				sectionSpecial.listPosition = listPosition++;
				sectionSpecial.id = inData.get(i).getId();

				this.add(sectionSpecial);
				menuListData.add(sectionSpecial);
			}
		}
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {

		int type = this.getItemViewType(position);
		final Item item = getItem(position);

		if (type == Item.SECTION) {

			convertView = inflater.inflate(R.layout.list_content_header, null);

			TextView tv_header_title = (TextView) convertView.findViewById(R.id.txt_header_title);

			if (item.pointTermBeans == null) {

				tv_header_title.setText("特殊考点");
			} else {

				tv_header_title.setText(inData.get(item.sectionPosition).getContent());

			}
		}

		if (type == Item.ITEM_NORMAL) {

			convertView = inflater.inflate(R.layout.fragment_test_detail, null);
			TextView tv_fragment_test_detail_point_itenm_num = (TextView) convertView
					.findViewById(R.id.tv_fragment_test_detail_point_itenm_num);
			TextView tv_fragment_test_detail_point_itenm_content = (TextView) convertView
					.findViewById(R.id.tv_fragment_grade_detail_point_item_content);
			TextView tv_fragment_test_detail_point_item_answer = (TextView) convertView
					.findViewById(R.id.tv_fragment_test_detaile_point_item_answer);
			CalendarCard sb_fragment_test_detail_point_itenm_score = (CalendarCard) convertView
					.findViewById(R.id.sb_fragment_grade_detail_point_item_score);

			if (showAnswer) {// 是否显示Point Answer
				if (null != item.answer) {
					tv_fragment_test_detail_point_item_answer.setText(item.answer);
					tv_fragment_test_detail_point_item_answer.setVisibility(View.VISIBLE);
				}
			} else {
				tv_fragment_test_detail_point_item_answer.setVisibility(View.GONE);
			}

			PointTermBean itemValue = (PointTermBean) item.bean;

			tv_fragment_test_detail_point_itenm_num.setText(getSort(itemValue.getSort()));
			tv_fragment_test_detail_point_itenm_content.setText(itemValue.getContent());

			if (itemValue.isScored()) {

				sb_fragment_test_detail_point_itenm_score.setPostion(Integer.parseInt(itemValue.getReal()));
				sb_fragment_test_detail_point_itenm_score.totlescore(Integer.parseInt(itemValue.getScore()));

			} else {

				sb_fragment_test_detail_point_itenm_score.totlescore(Integer.parseInt(itemValue.getScore()));

			}

			sb_fragment_test_detail_point_itenm_score.setON(new OnCellClickListener() {

				@Override
				public void clickDate(int d, float clickX, float clickY) {
					item.bean.setReal(String.valueOf(d));
					item.bean.setScored(true);
					Calendar cal = new GregorianCalendar(TimeZone.getTimeZone("GMT+8"));
					item.bean.setScoreTime(changetime2service(cal.getTimeInMillis()));
					inData.get(item.sectionPosition).getTest_term().set(item.positionOfParent, item.bean);
					mGetScoreInterface.afterClick(inData);
				}

				@Override
				public void changeDate(CustomDate date) {

				}
			});
			// sb_fragment_test_detail_point_itenm_score.setPostion(Integer.parseInt(itemValue.getReal()));
			// sb_fragment_test_detail_point_itenm_score.totlescore(Integer.parseInt(itemValue.getScore()));
		}

		if (type == Item.ITEM_SPECIAL) {

			// PointTermBean itemValue = (PointTermBean) item.bean;

			convertView = inflater.inflate(R.layout.listview_item_special, null);

			TextView item_number_spec = (TextView) convertView
					.findViewById(R.id.tv_fragment_test_detail_point_item_num_spec);
			TextView item_content_spec = (TextView) convertView
					.findViewById(R.id.tv_fragment_test_detail_point_item_content_spec);
			final EditText item_subtractPoint_spec = (EditText) convertView.findViewById(R.id.editPoint_spec);

			item_number_spec.setText(inData.get(item.sectionPosition).getSort());

			item_content_spec.setText(inData.get(item.sectionPosition).getTitle());

			GradePointBean_Net pointMsg = inData.get(item.sectionPosition);

			Utils.saveSharedPrefrences(mContext, pointMsg.getId(), pointMsg.getScore());

			String subtract = pointMsg.getSubtract();

			if ("-1".equalsIgnoreCase(subtract)) {

				item_subtractPoint_spec.setHint("目前未扣分");

			} else if (null != subtract) {

				item_subtractPoint_spec
						.setHint("已扣分数：" + ((null == subtract || "".equalsIgnoreCase(subtract)) ? "0" : subtract));

			}
			// Edit输入监听，大于满分，提示
			final String scoreStr = inData.get(item.sectionPosition).getScore();
			final int scoreInt = scoreStr == null ? 0 : Integer.parseInt(scoreStr);

			item_subtractPoint_spec.addTextChangedListener(new TextWatcher() {

				@Override
				public void onTextChanged(CharSequence s, int start, int before, int count) {
					Log.i("xxxxx", s.toString());
					String inputStr = s.toString().trim();
					int inputInt = -1;
					if (null != inputStr && !"".equalsIgnoreCase(inputStr)) {

						inputInt = Integer.parseInt(inputStr);

						if (inputInt > -1 && inputInt > scoreInt) {
							// showTips("扣除的分数不能大于满分:" + scoreInt);
							inputInt = scoreInt;
							showTips("扣除的分数不能大于满分:" + scoreInt);

							inputStr = inputStr.substring(0, inputStr.length() - 1);
							item_subtractPoint_spec.setText(inputStr);

						}
						inData.get(item.sectionPosition).setSubtract(inputStr);
						inData.get(item.sectionPosition).setScoreTime(changetime2service(System.currentTimeMillis()));

					}
					mGetScoreInterface.afterClick(inData);
				}

				@Override
				public void beforeTextChanged(CharSequence s, int start, int count, int after) {
					Log.i("bbbbb", s.toString());
				}

				@Override
				public void afterTextChanged(Editable s) {
					// String inputStr =
					// item_subtractPoint_spec.getText().toString().trim();
					// if (xcxx!=inputStr) {
					// int inputInt = -1;
					// if (null != inputStr && !"".equalsIgnoreCase(inputStr)) {
					//
					// try {
					// inputInt = Integer.parseInt(inputStr);
					//
					// if (inputInt > -1 && inputInt > scoreInt) {
					// // showTips("扣除的分数不能大于满分:" + scoreInt);
					// inputInt = scoreInt;
					// showTips("扣除的分数不能大于满分:" + scoreInt);
					// inputStr=inputStr.substring(0,inputStr.length()-1);
					// //xcxx=inputStr;
					// item_subtractPoint_spec.setText(inputStr);
					//
					// }
					// inData.get(item.sectionPosition).setSubtract(String.valueOf(inputInt));
					// inData.get(item.sectionPosition)
					// .setScoreTime(changetime2service(System.currentTimeMillis()));
					//
					// } catch (NumberFormatException e) {
					// // item_subtractPoint_spec.clearFocus();
					// showTips("扣除的分数不能大于满分:" + scoreInt);
					// item_subtractPoint_spec.setText(scoreStr);
					// }
					// }
					// xcxx=inputStr;
					// mGetScoreInterface.afterClick(inData);
					// }

				}
			});

		}
		return convertView;
	}

	private SweetAlertDialog dialog;

	private void showTips(String contentStr) {

		if (null == dialog) {
			dialog = new SweetAlertDialog(mContext, false);
			dialog.setCanceledOnTouchOutside(false);
			dialog.setContentText(contentStr);
			dialog.setConfirmClickListener(new OnSweetClickListener() {

				@Override
				public void onClick(SweetAlertDialog sweetAlertDialog) {
					dialog.dismiss();
				}
			});
			dialog.show();
		} else {
			if (!dialog.isShowing()) {
				dialog.setContentText(contentStr);
				dialog.show();
			}
		}

	}

	@Override
	public int getViewTypeCount() {

		return 3;
	}

	public String changetime2service(long localtime) {

		SimpleDateFormat format = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
		Long time = new Long(localtime + time_difference);
		String d = format.format(time);
		return d;
	}

	@Override
	public int getItemViewType(int position) {
		return getItem(position).type;
	}

	@Override
	public boolean isItemViewTypePinned(int viewType) {
		return viewType == Item.SECTION;
	}

	public ArrayList<Item> getMenuListData() {
		return menuListData;
	}

	public ArrayList<GradePointBean_Net> getModifiedData() {
		return inData;
	}

	private String getSort(String sort) {
		if (sort != null && Integer.parseInt(sort) < 10) {
			return "0" + sort;
		} else {
			return sort;
		}
	}

	/** 显示考点标准答案 */
	public void isShowAnswer() {
		showAnswer = true;
		this.notifyDataSetChanged();
	}

	/** 隐藏考点标准答案 */
	public void isHideAnswer() {
		showAnswer = false;
		this.notifyDataSetChanged();
	}

	public void setGetScoreInterface(GetScoreInterface socreInterface) {
		this.mGetScoreInterface = (GetScoreInterface) socreInterface;
	}

	public interface GetScoreInterface {
		public void afterClick(ArrayList<GradePointBean_Net> data);
	}

}
