from lightfm.data import Dataset

class Mappings:
    def __init__(self, dataset: Dataset) -> None:
        userid2row, _, itemid2col, _ = dataset.mapping()
        self.userid2row = userid2row
        self.itemid2col = itemid2col
        # Invert dictionaries to get mapping in other direction
        self.row2userid = {value: key for key, value in self.userid2row.items()}
        self.col2itemid = {v: k for k, v in self.itemid2col.items()}
        # Use like this: 
        # mappings = Mappings(dataset)
        # mappings.userid2row["axfafe24"]