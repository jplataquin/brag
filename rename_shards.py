import os
import re

replacements = [
    ('shards', 'diamonds'),
    ('shard', 'diamond'),
    ('Shards', 'Diamonds'),
    ('Shard', 'Diamond'),
    ('SHARDS', 'DIAMONDS'),
    ('SHARD', 'DIAMOND'),
]

# We want to replace these in a way that doesn't break other words if possible,
# but "shard" is unique enough in this context.
# To be safe, we can use regex with word boundaries for the base words, 
# but things like "ShardLedger" or "shards_balance" need to be caught.

# If we replace "shards" with "diamonds" first, then "shard" with "diamond",
# most cases should be covered.

def replace_in_file(file_path):
    with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()

    original_content = content
    for old, new in replacements:
        # Use regex to find the old word. 
        # We don't necessarily want word boundaries if it's part of a class name or variable.
        # But we must be careful not to replace "shard" inside "marshalling" if it existed (it doesn't).
        # Actually, "shard" is very specific.
        content = content.replace(old, new)

    if content != original_content:
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)
        return True
    return False

directories = ['app', 'config', 'database', 'resources', 'routes']

for directory in directories:
    for root, dirs, files in os.walk(directory):
        for file in files:
            file_path = os.path.join(root, file)
            if replace_in_file(file_path):
                print(f"Updated: {file_path}")
