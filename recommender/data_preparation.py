import pandas as pd

def get_data():
    data = pd.read_csv("interactions.csv", sep=";")
    fm_data = data[["user_id", "item_id"]]
    users = data[["user_id","user_name"]].set_index("user_id").drop_duplicates()
    user_ids = fm_data["user_id"].drop_duplicates()
    items = data[["item_id","item_name"]].set_index("item_id").drop_duplicates()
    item_ids = fm_data["item_id"].drop_duplicates()

    return data, fm_data, users, user_ids, items, item_ids
