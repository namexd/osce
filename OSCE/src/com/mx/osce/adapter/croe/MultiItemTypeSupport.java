package com.mx.osce.adapter.croe;

/**
 * 多种条目类型的支持接口
 *
 * @author yu
 */
public interface MultiItemTypeSupport<T> {

    /**
     * 返回item类型数量,如果是RecyclerView,不用关心此方法
     */
    int getViewTypeCount();

    /**
     * 通过 t或者position 来判断返回的item类型
     */
    int getItemViewType(int position, T t);

    /**
     * 根据viewType来返回对应的layoutId
     */
    int getItemLayout(int viewType);

}
