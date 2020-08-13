import pandas as pd
import numpy as np
from support_functions import get_top_sorted

def get_recoms(fm_data, items, dataset, model, mappings, user_id, num_recs):
    step = 10
    n_recoms_to_retrieve = step
    recom_storage = pd.DataFrame(columns=["user_id", "item_id"])
    not_enough_recoms_after_filter = True

    while not_enough_recoms_after_filter:
        # Prepare
        n_users, n_items = dataset.interactions_shape()
        scores = model.predict(user_ids=user_id, item_ids=np.arange(n_items))
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

        # Filter visited items
        visited_items = fm_data[fm_data["user_id"] == user_id]["item_id"].drop_duplicates()
        mask = new_recs.apply(lambda x: x["item_id"] not in visited_items.values, axis=1)
        new_recs = new_recs[mask]

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