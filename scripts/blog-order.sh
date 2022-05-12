#/bin/sh

cd "$(dirname "$0")"/..

i=0
for post in $(cat "./scripts/blog-order.txt"); do
    sed -i "1,/^+++$/s/^weight\s*=\s*[0-9]\+/weight = $i/" "./content/blog/$post/index.md"
    let 'i=i+1'
done
