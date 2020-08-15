import pandas as pd
import numpy as np
from support_functions import get_top_sorted

from sklearn.metrics.pairwise import cosine_similarity

def get_recoms(fm_data, items, dataset, model, mappings, user_id, num_recs):
    step = 10
    n_recoms_to_retrieve = step
    recom_storage = pd.DataFrame(columns=["user_id", "item_id"])
    not_enough_recoms_after_filter = True

    while not_enough_recoms_after_filter:
        # Prepare
        n_users, n_items = dataset.interactions_shape()
        user_row = mappings.userid2row[user_id]
        scores = model.predict(user_ids=user_row, item_ids=np.arange(n_items))
        sorted_scores = get_top_sorted(scores, n_recoms_to_retrieve) # Get n_recoms_to_retrieve top recommendations
        sorted_scores = sorted_scores[-step:] # Keep only newest step recommendations (as only these are new)

        # Recommendations to DataFrame
        new_recs = pd.DataFrame(columns=["user_id", "item_id"])
        for tup in sorted_scores:
            item_id = mappings.col2itemid[tup[0]]
            new_recs = new_recs.append({
                "user_id": user_id,
                "item_id": item_id,
            }, ignore_index=True)

        # Filter recommendations (delete already visited ones)
        new_recs = delete_visted_modules(fm_data=fm_data, user_id=user_id, recs=new_recs)

        # Add new recs to storage
        recom_storage = recom_storage.append(new_recs)
        
        # Finish if storage is full enough. Otherwise look at next step recommendations.
        if(recom_storage.shape[0] >= num_recs):
            not_enough_recoms_after_filter = False
        else:
            n_recoms_to_retrieve = n_recoms_to_retrieve + step
            # Break if no more items left
            if (n_recoms_to_retrieve > items.shape[0]):
                print(f"Less than {num_recom} items available after filtering")
                return recom_storage

    return recom_storage.head(num_recs)

def delete_visted_modules(fm_data, user_id, recs):
    """
    recs must be DataFrame with column item_id
    """
    visited_items = fm_data[fm_data["user_id"] == user_id]["item_id"].drop_duplicates()
    mask = recs.apply(lambda x: x["item_id"] not in visited_items.values, axis=1)
    recs = recs[mask]
    return recs

# def get_similar_modules(items, model, mappings, module_id, num_of_rec):
#     module_col = mappings.itemid2col[module_id]
#     simi_modules = cosine_similarity(model.get_item_representations()[1])
#     simi_tuples = get_top_sorted(simi_modules.round(2)[module_col], num_of_rec)

#     df = pd.DataFrame(columns=["module_name", "score"])
#     for x in simi_tuples:
#         module_id = mappings.col2itemid[x[0]]
#         name = items.loc[module_id]
#         score = np.round(float(x[1]), decimals=2)
#         df = df.append({
#             "module_name": name,
#             "score": score,
#         }, ignore_index=True)

#     return df


def get_similar_modules(items, model, mappings, base_item_id, num_of_rec):
    module_col = mappings.itemid2col[base_item_id]
    simi_modules = cosine_similarity(model.get_item_representations()[1])
    simi_tuples = get_top_sorted(simi_modules.round(2)[module_col], num_of_rec+1) # Compensate that it will recommend item itself

    # Recommendations to DataFrame
    recs = pd.DataFrame(columns=["base_item_id", "item_id"])
    for tup in simi_tuples:
        item_id = mappings.col2itemid[tup[0]]
        recs = recs.append({
            "base_item_id": base_item_id,
            "item_id": item_id,
        }, ignore_index=True)

    recs = recs[recs["item_id"] != base_item_id]

    recs["rank"] = np.arange(1, num_of_rec+1)

    return recs


# def get_similar_modules(fm_data, items, user_id, model, mappings, base_item_id, num_of_rec):
#     step = 10
#     n_recoms_to_retrieve = step
#     recom_storage = pd.DataFrame(columns=["base_item_id", "item_id"])
#     not_enough_recoms_after_filter = True

#     while not_enough_recoms_after_filter:
#         # Prepare
#         module_col = mappings.itemid2col[base_item_id]
#         simi_modules = cosine_similarity(model.get_item_representations()[1])
#         simi_tuples = get_top_sorted(simi_modules.round(2)[module_col], n_recoms_to_retrieve)
#         simi_tuples = simi_tuples[-step:]

#         # Recommendations to DataFrame
#         new_recs = pd.DataFrame(columns=["base_item_id", "item_id"])
#         for tup in simi_tuples:
#             item_id = mappings.col2itemid[tup[0]]
#             new_recs = new_recs.append({
#                 "base_item_id": base_item_id,
#                 "item_id": item_id,
#             }, ignore_index=True)

#         new_recs = new_recs[new_recs["item_id"] != base_item_id]

#         # Filter recommendations (delete already visited ones)
#         new_recs = delete_visted_modules(fm_data=fm_data, user_id=user_id, recs=new_recs)

#         # Add new recs to storage
#         recom_storage = recom_storage.append(new_recs)
        
#         # Finish if storage is full enough. Otherwise look at next step recommendations.
#         if(recom_storage.shape[0] >= num_of_rec):
#             not_enough_recoms_after_filter = False
#         else:
#             n_recoms_to_retrieve = n_recoms_to_retrieve + step
#             # Break if no more items left
#             if (n_recoms_to_retrieve > items.shape[0]):
#                 print(f"Less than {num_of_rec} items available after filtering")
#                 return recom_storage

#     return recom_storage.head(num_of_rec)