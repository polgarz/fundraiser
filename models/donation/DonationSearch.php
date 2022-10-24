<?php

namespace app\models\donation;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\donation\Donation;

/**
 * DonationSearch represents the model behind the search form of `app\models\donation\Donation`.
 */
class DonationSearch extends Donation
{
    const GENERAL_DONATION = 'general';

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'anonymous', 'newsletter', 'vendor_ref', 'recurring', 'parent_id'], 'integer'],
            [['hash', 'payment_method', 'name', 'email', 'campaign_ambassador_id', 'campaign_id', 'message', 'status', 'token', 'token_due_date', 'created_at'], 'safe'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param boolean $export
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Donation::find()
            ->with(['donations', 'parent.donations'])
            ->joinWith(['campaign', 'ambassador'])
            ->where(['token' => null]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC
                ]
            ],
            'pagination' => ['pageSize' => 100],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'donation.id' => $this->id,
            'user_id' => $this->user_id,
            'amount' => $this->amount,
            'anonymous' => $this->anonymous,
            'newsletter' => $this->newsletter,
            'vendor_ref' => $this->vendor_ref,
            'recurring' => $this->recurring,
            'parent_id' => $this->parent_id,
            'token_due_date' => $this->token_due_date,
        ]);

        if ($this->created_at) {
            $dates = explode(' - ', $this->created_at);
            $query->andFilterWhere(['between', 'donation.created_at', $dates[0], $dates[1]]);
        }

        if ($this->campaign_id == self::GENERAL_DONATION) {
            $query->andWhere(['is', 'donation.campaign_id', null]);
        } elseif ($this->campaign_id) {
            $query->andWhere(['like', 'donation.campaign_id', $this->campaign_id]);
        }

        $query->andFilterWhere(['like', 'hash', $this->hash])
            ->andFilterWhere(['like', 'payment_method', $this->payment_method])
            ->andFilterWhere(['like', 'donation.name', $this->name])
            ->andFilterWhere(['like', 'campaign_ambassador.name', $this->campaign_ambassador_id])
            ->andFilterWhere(['like', 'donation.email', $this->email])
            ->andFilterWhere(['like', 'donation.status', $this->status]);

        return $dataProvider;
    }
}
