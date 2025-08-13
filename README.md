# Word List Comparator
# Author: Roberto Aleman, ventics.com

A simple and efficient web tool for comparing two lists of words. It allows users to identify common, exclusive, and duplicate words, and offers the option to generate new, unified lists.

---

## ✨ Key Features

* **User-Friendly Interface**: Allows users to upload text files or paste word lists directly.
* **Comprehensive Analysis**: Provides a detailed report that includes:
    * **Internally Duplicated Words**: Identifies words that are repeated within each list.
    * **Exclusive Words**: Shows words that are only in List 1 or only in List 2.
    * **Common Words**: Lists words that appear in both lists, indicating their first position and number of repetitions.
* **Data Normalization**: Performs case-insensitive comparisons and cleans content by removing whitespace and empty lines.
* **File Generation**: Allows the creation of a text file (`newlist.txt`) with words found in both lists.
* **List Unification**: Offers the option to display a unified list with all unique words from both lists.

---

## 🚀 How to Use

Simply upload the `index.php` and `ListComparator.php` files to the same folder on your web server. Then, open `index.php` in your browser.

1.  **Enter Lists**: You can choose one of two options to input your data:
    * **Upload Files**: Use the "upload a text file" fields to select two files from your computer.
    * **Copy and Paste**: Paste the words directly into the provided text areas.
2.  **Configure Options**: Check the boxes according to your needs:
    * `Unify lists with unique occurrences`: Displays a list with all unique words from both lists.
    * `Create a new file with common words`: Generates a file named `newlist.txt` with words found in both lists.
3.  **Compare**: Click the `Compare Lists` button to run the analysis. The results will be displayed directly on the web page.

---

## 🛠️ Project Structure

* `index.php`: Contains the user interface (HTML) and the logic to process form data. This is the main file users interact with.
* `ListComparator.php`: Defines the `ListComparator` class, which encapsulates all the business logic: data normalization, list comparison, repetition counting, and the creation of the new text file.

---

## 📄 License

This project is released under the GNU AFFERO GENERAL PUBLIC LICENSE. You are free to use, modify, and distribute it for any purpose.