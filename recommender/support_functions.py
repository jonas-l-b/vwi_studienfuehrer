import numpy as np

def get_top_sorted(scores: np.ndarray, top_n):
    """Get the top indices sorted descendingly from the scores list array.

    Args:
        scores: An array with scores.
        top_n: The number of top scores to be returned.

    Returns:
        ScoringList: The first element of the tuple is the index where the score was
                in the original array, the second element is the score itself.
    """
    # This is a very efficient way to get the Top-N elements. The -Nth element gets sorted into its correct position in the array. Then, all smaller or equal elements are to the left,
    #  all bigger ones to the right.
    best_idxs = np.argpartition(scores, -top_n)[-top_n:]
    # TODO: sorted() returns a list but I think it would be better to return a
    #  generator
    # Sort descending by the second element in the zip-tuple which is the score.
    return sorted(zip(best_idxs, scores[best_idxs]), key=lambda x: -x[1])